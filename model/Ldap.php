<?php

    // Este objeto se encarga de registrar el dominio Ldap
    namespace model;

    use model\Authenticate;
    use model\Group;
    use model\Person;
    use model\OrganizationalUnit;
    use model\Device;

    class Ldap
    {

        private $firstname;
        private $lastname;
        private $password;
        private $domainName;
        public $dotenv;
        private $fullName;
        private $userPhoto;
        private $tel;
        private $address;
        private $salt;
        private $domNumberId;

        public function __construct(string $firstname=null, string $lastname=null, string $password=null, string $domainName=null, string $userPhoto=null, string $tel=null, string $address=null){
            $this->fullName="$firstname $lastname";
            if (($first = strstr($firstname, ' ', true)) !== false && ($last = strstr($lastname, ' ', true)) !== false){
                $this->firstname=strtolower($first);
                $this->lastname=strtolower($last);
            }else{
                $this->firstname=strtolower($firstname);
                $this->lastname=strtolower($lastname);
            }
            $this->password=$password;
            $this->domainName=$domainName;
            $this->dotenv=parse_ini_file('.env');
            $this->userPhoto=$userPhoto;
            $this->tel=$tel;
            $this->address=$address;
            $this->salt=openssl_random_pseudo_bytes(4);
            $this->domNumberId=null;
        }

        public function modifyEnvFile(string $varName, string $valueToModify){

            $file = file('.env');

            foreach ($file as &$line){
                if(strpos($line, $varName.'=') !== false){
                    $line = $varName.'='.$valueToModify . PHP_EOL;
                    break;
                }
            }
            
            file_put_contents('.env', implode('', $file));

        }

        public function addNewLdapDomain(){
            $responseError = "";
            $divideDomain=explode(".", $this->domainName);
            if(count($divideDomain) == 2){
                $numId = "$divideDomain[0].$divideDomain[1]";
    
                $totalDomain=$this->dotenv["TOTAL_DOMAIN"];
                $number=intval($totalDomain);
                $number++;
                $this->domNumberId=$number;
    
                $createDomainLdif = "'dn: olcDatabase={".$number."}mdb,cn=config
objectClass: olcDatabaseConfig
objectClass: olcMdbConfig
olcDatabase: {".$number."}mdb
olcDbDirectory: /var/lib/ldap/$numId
olcSuffix: dc=$divideDomain[0],dc=$divideDomain[1]
olcRootDN: cn=$this->firstname.$this->lastname@$divideDomain[0].$divideDomain[1],dc=$divideDomain[0],dc=$divideDomain[1]
olcRootPW: ".exec('slappasswd -s "'.$this->password.'"')."
olcDbIndex: objectClass eq,pres
olcDbIndex: uid pres,eq
olcDbIndex: mail pres,eq
olcDbIndex: cn,sn,displayName pres,sub,eq
olcDbCheckpoint: 512 30
olcAccess: {0}to attrs=userPassword by self write by anonymous auth by * none
olcAccess: {1}to attrs=shadowLastChange by self write by * read
olcAccess: {2}to * by * read'";
    
                $this->modifyEnvFile("TOTAL_DOMAIN", strval($number));
                

                $responseError = $this->settingLdapServer($numId, $createDomainLdif, $divideDomain);
                

            }else{
                $responseError = "Ha ocurrido un error con la validación del dominio, porfavor introduce el nombre de dominio correctamente";
            }

            return $responseError;

        }

        // Configuraciones realizada en el servidor
        public function settingLdapServer(string $directoryID, string $objectDomain, array $divideDomain){

            $responseError = "";
            
            if (!function_exists("ssh2_connect")) die("La librería ssh2 no está instalada.");

            // Datos del servidor remoto
            $server = $this->dotenv["LDAP_SERVER_ROUTE"];
            $username = $this->dotenv["LDAP_ROOT_USER"];
            $password = $this->dotenv["LDAP_ROOT_PASSWORD"];
            $uidUser = $this->dotenv["UID_NUMBER"];
            $gidNumber = $this->dotenv["GID_NUMBER"];

            // Establecemos la conexión con el servidor remoto
            $conn = ssh2_connect($server, 22);

            // Autenticamos con el usuario y contraseña
            if (ssh2_auth_password($conn, $username, $password)) {

                $ous =  $this->addOrganizationalUnit($divideDomain);
                $addOwnerUserAndGroup = $this->addOwnerUserLdap(intval($uidUser), intval($gidNumber), $divideDomain);

                $adminDnUser = "$this->firstname.$this->lastname@$divideDomain[0].$divideDomain[1]";
                // Creamos el directorio en el servidor remoto
                $stream = ssh2_exec($conn, "bash /home/$username/system_files_exec/exec_command.sh $password $directoryID $objectDomain $ous $adminDnUser $this->password $divideDomain[0] $divideDomain[1] $addOwnerUserAndGroup $this->userPhoto");

                stream_set_blocking($stream, true);
                stream_get_contents($stream);
                
                // Añadir el nuevo arbol en el servidor LDAP
                ssh2_disconnect($conn);

            } else {
                $responseError = "Lamentamos informarle que en estos momentos no ha sido posible establecer conexión con el servidor Ldap. Por favor, inténtelo de nuevo más tarde o póngase en contacto con nuestro equipo de soporte técnico para obtener ayuda adicional. Agradecemos su paciencia y comprensión.";
            }

            return $responseError;
        }
        
        public function addOrganizationalUnit(array $divideDomain){
            
            $ous = "'dn: dc=$divideDomain[0],dc=$divideDomain[1]
objectClass: top
objectClass: dcObject
objectClass: organization
o: $divideDomain[0]
dc: $divideDomain[0]

dn: ou=users,dc=$divideDomain[0],dc=$divideDomain[1]
objectClass: organizationalUnit
objectClass: top
ou: users
description: Guarda todos los usuarios del dominio.

dn: ou=groups,dc=$divideDomain[0],dc=$divideDomain[1]
objectClass: organizationalUnit
objectClass: top
ou: groups
description: Guarda todos lo grupos del dominio

dn: ou=devices,dc=$divideDomain[0],dc=$divideDomain[1]
objectClass: organizationalUnit
objectClass: top
ou: devices
description: Guarda todos los equipos del dominio

dn: ou=policies,dc=$divideDomain[0],dc=$divideDomain[1]
objectClass: organizationalUnit
objectClass: top
ou: policies
description: Guarda todas las politicas de seguridad del domino'";

            return $ous;

        }

        // // Add users

        public function addOwnerUserLdap(int $uidNumber, int $gidNumber, array $divideDomain){

            $urlPhotoEncode = base64_encode("/var/lib/ldap/$this->userPhoto");

            $userLdif = "'dn: uid=$this->firstname.$this->lastname@$divideDomain[0].$divideDomain[1],ou=users,dc=$divideDomain[0],dc=$divideDomain[1]
objectClass: top
objectClass: posixAccount
objectClass: inetOrgPerson
uid: $this->firstname.$this->lastname@$divideDomain[0].$divideDomain[1]
cn: $this->fullName
sn: $this->lastname
givenName: $this->firstname
displayName: $this->fullName
uidNumber: $uidNumber
gidNumber: $gidNumber
userPassword: ".exec('slappasswd -s "'.$this->password.'"')."
gecos: $this->fullName
loginShell: /bin/bash
homeDirectory: /home/$this->firstname-$this->lastname
mail: $this->firstname.$this->lastname@$divideDomain[0].$divideDomain[1]
telephoneNumber: $this->tel
jpegPhoto:: $urlPhotoEncode
homePostalAddress: $this->address
employeeNumber: $this->domNumberId

dn: cn=owners,ou=groups,dc=$divideDomain[0],dc=$divideDomain[1]
objectClass: posixGroup
cn: owners
gidNumber: $gidNumber'";


            $num1=$uidNumber+1;
            $num2=$gidNumber+1;

            $this->modifyEnvFile("UID_NUMBER", strval($num1));
            $this->modifyEnvFile("GID_NUMBER", strval($num2));

            return $userLdif;
       
        }

        // Esta funcion devuelve todos los usuarios en un objeto
        public static function getUsersLdap(string $ldapHost, string $username, string $password, string $ouUsersDN){
            $_SESSION['users']=[];
            $conn = new Connection();

            if ($conn->binding($username, $password)){

                $entries =  $conn->search($ouUsersDN,  "(objectClass=inetOrgPerson)");


                for($i=0; $i<$entries['count']; $i++){
                    $status = true;

                    if(array_key_exists('pwdAccountLockedTime', $entries[$i])){
                        $status=false;
                    }

                    $parts = explode('/', $entries[$i]["jpegphoto"][0]);
                    $file = end($parts);

                    array_push($_SESSION['users'], new Person($entries[$i]['cn'][0], $entries[$i]['uid'][0], $entries[$i]['uidnumber'][0], $entries[$i]['jpegphoto'][0], $entries[$i]['telephonenumber'][0], $entries[$i]['dn'], $status, $entries[$i]['sn'][0], $entries[$i]['givenname'][0], $entries[$i]['homepostaladdress'][0]));

                }
                $conn->unbind();

            }

        }

        // Esta funcion devuelve todos los grupos del dominio en un array
        public static function getGroupsLdap(string $ldapHost, string $username, string $password, string $ouGroupDN){
            $_SESSION['groups']=[];

            $conn = new Connection();
            // Codigo para el buscador

            if ($conn->binding($username, $password)){

                $entries =  $conn->search($ouGroupDN,  "(objectClass=posixGroup)");
                $conn->unbind();

                for($i=0; $i<$entries['count']; $i++){
                    array_push($_SESSION['groups'], new Group($entries[$i]['cn'][0], $entries[$i]['gidnumber'][0], isset($entries[$i]['memberuid'])?$entries[$i]['memberuid']:[], $entries[$i]['dn']));
                }
            }

        }

        // Obtiene las unidades organizativas del usuario actual
        public static function getOrgUnitLdap(string $ldapHost, string $username, string $password, string $ousDN){
            $_SESSION['organizationalUnit']=[];

            $conn = new Connection();
            // Codigo para el buscador

            if ($conn->binding($username, $password)){
                $entries = $conn->search($ousDN,  "(objectClass=organizationalUnit)");

                for($i=0; $i<$entries['count']; $i++){

                    $ouEntries =  $conn->search($entries[$i]['dn'],  "(objectclass=*)");
                    if($ouEntries['count'] == 0){
                        $ouEntries = [];
                    }

                    array_push($_SESSION['organizationalUnit'], new OrganizationalUnit($entries[$i]['ou'][0], $entries[$i]['dn'], $entries[$i]['description'][0], $ouEntries));
                }

                $conn->unbind();
            }

        }
        
        public static function getDevicesLdap(string $ldapHost, string $username, string $password, string $treeDN){
            $_SESSION['devices']=[];

            $conn = new Connection();
            // Codigo para el buscador
            if ($conn->binding($username, $password)){
                $entries = $conn->search($treeDN,  "(objectClass=device)");

                for($i=0; $i<$entries['count']; $i++){
                    array_push($_SESSION['devices'], new Devices($entries[$i]["dn"], $entries[$i]['cn'][0], $entries[$i]['description'][0], $entries[$i]['iphostnumber'][0]));
                }
                $conn->unbind();
            }

        }


        public function editObjectLdap(string $ldapHost, string $username, string $password, array $postdata, string $controlAction, string $dominio, $file){
            $dn = explode(".", $dominio);

            $ldapConn = ldap_connect($ldapHost);
            if(!$ldapConn) die("Error al conectar con el servidor LDAP");
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION,3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS,0);

            $bind = @ldap_bind($ldapConn, $username, $password);
            if ($bind){
                switch($controlAction){
                    case 'users':
                        $modUserDn = $postdata['user-dn'];

                        $reSearch=ldap_search($ldapConn, $modUserDn, "(objectClass=*)");
                        $entries = ldap_get_entries($ldapConn, $reSearch);

                     

                        if ($entries['count'] > 0){
                            if($postdata['changeOU'] != "" && "$this->firstname.$this->lastname@$dominio" == $entries[0]['uid'][0]){
                                // ldap_delete($ldapConn, $entries[0]['dn']);
                                $newDN = "uid=$this->firstname.$this->lastname@$dominio";
                                $newDnParent = $postdata['changeOU'];
                                $resMod=ldap_rename($ldapConn, $entries[0]['dn'], $newDN, $newDnParent, true);
                                echo ldap_error($ldapConn);
                            }

                            $entry=array();
                            if($this->firstname != $entries[0]['givenname'][0] || $this->lastname != $entries[0]['sn'][0]){
                                if($postdata['changeOU'] != ""){
                                    $newOuForUser = $postdata['changeOU'];
                                }else{
                                    $ou_pos = strpos($entries[0]['dn'], "ou");
                                    if($ou_pos !== false){
                                        $newOuForUser=substr($entries[0]['dn'], $ou_pos);
                                    }
                                }

                                $newDN = "uid=$this->firstname.$this->lastname@$dominio,".$newOuForUser;
                                ldap_delete($ldapConn, $modUserDn);
                                $entry = array();
                                $entry["objectClass"][0] = "top";
                                $entry["objectClass"][1] = "inetOrgPerson";
                                $entry["objectClass"][2] = "posixAccount";
                                $entry["uid"] = "$this->firstname.$this->lastname@$dominio";
                                $entry["givenName"] = $this->firstname;
                                $entry["sn"] = $this->lastname;
                                $entry["cn"] = $this->fullName;
                                $entry["displayName"] = $this->fullName;
                                $entry["uidNumber"] = $entries[0]['uidnumber'][0];
                                $entry["gidNumber"] = $entries[0]['gidnumber'][0];
                                $entry["homeDirectory"] = "/home/$this->firstname-$this->lastname";
                                $entry["loginShell"] = "/bin/bash";
                                $entry["userPassword"] = $entries[0]['userpassword'][0];
                                $entry["homePostalAddress"] = $entries[0]['homepostaladdress'][0];
                                $entry["gecos"]="$this->fullName";
                                $entry["jpegPhoto"] = $entries[0]['jpegphoto'][0];
                                $entry["telephoneNumber"] = $entries[0]['telephonenumber'][0];
                                $entry["mail"] = "$this->firstname.$this->lastname@$dominio";
                                $resMod = ldap_add($ldapConn, $newDN, $entry);
                            }else{
                                
                                if($postdata['password'] != ""){$entry['userPassword']=exec('slappasswd -s "'.$postdata['password'].'"');}
                                // else if($postdata['file'] != ""){
                                //     $entry['jpegPhoto']=base64_encode($this->save_file_to_server($postdata['firstname'], $file));
                                // }
                                $entry['telephoneNumber']=$postdata['tel'];
                                $entry['homePostalAddress']=$postdata['address'];

                                $resMod = ldap_modify($ldapConn, $modUserDn, $entry);
                            }
                        }
                        ldap_unbind($ldapConn);

                        // echo isset($file);
                        // print_r($postdata);
                        // print_r($entry);


                        break;
                    case 'groups':
                        $modDnGroup = $postdata['group-dn'];
                        if (($first = strstr($postdata['name-group'], ' ', true)) !== false){
                            $postdata['name-group']=strtolower($first);
                        }else{
                            $postdata['name-group']=strtolower($postdata['name-group']);
                        }

                        $reSearch=ldap_search($ldapConn, $modDnGroup, "(objectClass=*)");
                        $entries = ldap_get_entries($ldapConn, $reSearch);

                        

                        if ($entries['count'] > 0){

                            if($postdata['changeOU'] != "" && $postdata['name-group'] == $entries[0]['cn'][0]){
                                $newDN = "cn=".$entries[0]['cn'][0];
                                $newDnParent = $postdata['changeOU'];
                                $resMod=ldap_rename($ldapConn, $entries[0]['dn'], $newDN, $newDnParent, true);
                                echo ldap_error($ldapConn);
                            }

                            $entry=array();
                            if($postdata['name-group'] != $entries[0]['cn'][0]){

                                if($postdata['changeOU'] != ""){
                                    $newOuForGroup = $postdata['changeOU'];
                                }else{
                                    $ou_pos = strpos($entries[0]['dn'], "ou");
                                    if($ou_pos !== false){
                                        $newOuForGroup=substr($entries[0]['dn'], $ou_pos);
                                    }
                                }


                                $newDN = "cn=".$postdata['name-group'];
    
                                $entry['cn']=$postdata['name-group'];
                                $entry['gidNumber']=$entries[0]['gidnumber'][0];

                                ldap_modify($ldapConn, $modDnGroup, $entry);
                                $resMod=ldap_rename($ldapConn, $modDnGroup, $newDN, null, true);
                            }else{
                                // Añadir ldap usuario al grupo
                                if(isset($postdata['users'])){
                                    if(!isset($entries[0]['memberuid'])){
                                        $entry['memberUid']=$postdata['users'];
                                        $resMod = ldap_modify($ldapConn, $modDnGroup, $entry);
                                    }else{
                                        $existingMembers = $entries[0]['memberuid'];
                                        unset($existingMembers['count']);
                                        $allUid = array_merge($existingMembers, $postdata['users']);
                                        $entry['memberUid']=isset($allUid)?$allUid:$existingMembers;
                                        $resMod = ldap_mod_replace($ldapConn, $modDnGroup, $entry);
                                    }
                                }
                                // Eliminar ldap usuario del grupo
                                if(isset($postdata['delUsers'])){
                                    $existingMembers = $entries[0]['memberuid'];
                                    unset($existingMembers['count']);
                                    for($i=0; $i<count($postdata['delUsers']); $i++){
                                        if(in_array($postdata['delUsers'][$i], $existingMembers)){
                                            $allUid = [];
                                            array_push($allUid, $postdata['delUsers'][$i]);
                                        }
                                    }
                                    $entry['memberUid']=$allUid;
                                    $resMod = ldap_mod_del($ldapConn, $modDnGroup, $entry);
                                }
                            }


                            if($resMod){
                                echo "El grupo se ha añadido correctamente";
                            }else{
                                echo "Ha ocurrido un error al guardar el grupo";
                            }
                        }
                        ldap_unbind($ldapConn);

                        break;
                    case 'organizationalUnit':
                        $modDnOu = $postdata['ou-dn'];
                        if (($first = strstr($postdata['name-ou'], ' ', true)) !== false){
                            $postdata['name-ou']=strtolower($first);
                        }else{
                            $postdata['name-ou']=strtolower($postdata['name-ou']);
                        }


                        $reSearch = ldap_search($ldapConn, $modDnOu, "(objectClass=*)");
                        $entries = ldap_get_entries($ldapConn, $reSearch);

                        if($entries['count'] > 0){

                            $entry=array();
                            // Este if modifica en caso de que cambien el nombre.
                            if($postdata['name-ou'] != $entries[0]['ou'][0]){
                                $ouSearch=ldap_search($ldapConn, $entries[0]['dn'], '(!(objectClass=organizationalUnit))');
                                $ouEntries=ldap_get_entries($ldapConn, $ouSearch);

                                if($ouEntries['count']>0){
                                    $newOu="ou=".$postdata['name-ou'];
                                    for($i=0; $i<$ouEntries['count']; $i++){
                                        // ldap_delete($ldapConn, $ouEntries[$i]['dn']);
                                        
                                        $old_dn=$ouEntries[$i]['dn'];

                                        $ouEntries[$i]['dn'] = preg_replace('/^([^,]*),ou=([^,]*),(.*)$/', '${1},ou='.$postdata['name-ou'].',${3}', $ouEntries[$i]['dn']);
                                        
                                        $parts = explode(',', $ouEntries[$i]['dn'], 2);
                                        ldap_rename($ldapConn, $old_dn, $parts[0], $parts[1], true);
                                    }

                                    $newDN = "ou=".$postdata['name-ou'];
                                    $resMod=ldap_rename($ldapConn, $entries[0]['dn'], $newDN, null, true);



                                }else{
                                    $newDN = "ou=".$postdata['name-ou'];
                                    $entry["ou"]=$postdata['name-ou'];
                                    $entry["description"]=$postdata['description-ou'] != $entries[0]['description'][0]?$postdata['description-ou']:$entries[0]['description'][0];
    
                                    ldap_modify($ldapConn, $entries[0]['dn'], $entry);
                                    $resMod=ldap_rename($ldapConn, $entries[0]['dn'], $newDN, null, true);
                                    echo ldap_error($ldapConn);
                                }

                            }else if($postdata['changeOU'] != ""){
                                $newDN = "ou=".$entries[0]['ou'][0];
                                $newDnParent = $postdata['changeOU'];
                                $resMod=ldap_rename($ldapConn, $entries[0]['dn'], $newDN, $newDnParent, true);
                            }
                            else{
                                $entry['description']=$postdata['description-ou'];
                                $resMod = ldap_modify($ldapConn, $modDnOu, $entry);
                            }
                        }

                        if($resMod){
                            echo "La ou se ha modificado correctamente";
                        }else{
                            echo "Ha ocurrido un error al modificar la ou";
                        }
            
                        ldap_unbind($ldapConn);
                        break;
                    case 'devices':
                        $modDnDevices = $postdata['devices-dn'];


                        if (($first = strstr($postdata['devices-name'], ' ', true)) !== false){
                            $postdata['devices-name']=strtolower($first);
                        }else{
                            $postdata['devices-name']=strtolower($postdata['devices-name']);
                        }

                        $reSearch = ldap_search($ldapConn, $modDnDevices, "(objectClass=device)");
                        $entries = ldap_get_entries($ldapConn, $reSearch);

                        if($entries['count']>0){

                            $entry=array();

                            if($postdata['devices-name'] != $entries[0]['cn'][0]){

                                $entry['cn']=$postdata['devices-name'];
                                $entry['description']=$postdata['description-devices'];
                                $entry['ipHostNumber']=$postdata['iphost-devices'];

                                $newName="cn=".$postdata['devices-name'];
                                
                                // print_r($entry);

                                ldap_modify($ldapConn, $modDnDevices, $entry);
                                $resMod=ldap_rename($ldapConn, $modDnDevices, $newName, null, true);
                            }else if($postdata['changeOU'] != ""){
                                $newDN = "cn=".$entries[0]['cn'][0];
                                $newDnParent = $postdata['changeOU'];

                                $resMod=ldap_rename($ldapConn, $entries[0]['dn'], $newDN, $newDnParent, true);
                            }else{
                                $entry['description']=$postdata['description-devices'];
                                $entry['ipHostNumber']=$postdata['iphost-devices'];
                                echo $modDnDevices;
                                $resMod = ldap_modify($ldapConn, $modDnDevices, $entry);
                                echo ldap_error($ldapConn);
                            }

                        }
                        ldap_unbind($ldapConn);
                        
                        if($resMod){
                            echo "La ou se ha modificado correctamente";
                        }else{
                            echo "Ha ocurrido un error al modificar la ou";
                        }

                        break;
                }

            }

        }

        public function deleteObjectLdap(string $ldapHost, string $username, string $password, string $dn_object, string $controAction, $dominio){

            $ldapConn = ldap_connect($ldapHost);
            if(!$ldapConn) die("Error al conectar con el servidor LDAP");
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION,3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS,0);

            $bind = @ldap_bind($ldapConn, $username, $password);
            if ($bind){
                switch($controAction){
                    case 'users':
                        ldap_delete($ldapConn, $dn_object);
                        echo "Se elimino correctamente";
                        break;
                    case 'groups':
                        ldap_delete($ldapConn, $dn_object);
                        break;
                    case 'organizationalUnit':
                        ldap_delete($ldapConn, $dn_object);
                        break;
                }
            }


        }

        public function addObjectLdap(string $ldapHost, string $username, string $password, array $postdata, string $controlAction, string $dominio, $file){

            $uidNumber = $this->dotenv["UID_NUMBER"];
            $gidNumber = $this->dotenv["GID_NUMBER"];
            $dn = explode(".", $dominio);

            $ldapConn = ldap_connect($ldapHost);
            if(!$ldapConn) die("Error al conectar con el servidor LDAP");
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION,3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS,0);

            $bind = @ldap_bind($ldapConn, $username, $password);
            if ($bind){
                switch($controlAction){
                    case 'users':

                        $userDn = "uid=$this->firstname.$this->lastname@$dominio,ou=users,dc=$dn[0],dc=$dn[1]";

                        $entry = array();
                        $entry["objectClass"][0] = "top";
                        $entry["objectClass"][1] = "inetOrgPerson";
                        $entry["objectClass"][2] = "posixAccount";
                        $entry["uid"] = "$this->firstname.$this->lastname@$dominio";
                        $entry["givenName"] = $this->firstname;
                        $entry["sn"] = $this->lastname;
                        $entry["cn"] = $this->fullName;
                        $entry["displayName"] = $this->fullName;
                        $entry["uidNumber"] = $uidNumber;
                        $entry["gidNumber"] = $gidNumber;
                        $entry["homeDirectory"] = "/home/$this->firstname-$this->lastname";
                        $entry["loginShell"] = "/bin/bash";
                        $entry["userPassword"] = exec('slappasswd -s "'.$postdata['password'].'"');
                        $entry["homePostalAddress"] = $postdata['address'];
                        $entry["gecos"]="$this->fullName";
                        $entry["jpegPhoto"] = base64_encode($this->save_file_to_server($postdata['firstname'], $file));
                        $entry["telephoneNumber"] = strval($postdata['tel']);
                        $entry["mail"] = "$this->firstname.$this->lastname@$dominio";

                        
                        $res_add = ldap_add($ldapConn, $userDn, $entry);

                        echo ldap_error($ldapConn);
                        ldap_unbind($ldapConn);

                        if($res_add){
                            $num1=$uidNumber+1;
                            $num2=$gidNumber+1;

                            $this->modifyEnvFile("GID_NUMBER", strval($num2));
                            $this->modifyEnvFile("UID_NUMBER", strval($num1));
                            echo "El usuario se añadio correctamente";
                        }else{
                            echo "Ha ocurrido un error al añadir el usuario";
                        }

                        break;
                    case 'groups':
                        if (($first = strstr($postdata['name-group'], ' ', true)) !== false){
                            $postdata['name-group']=strtolower($first);
                        }else{
                            $postdata['name-group']=strtolower($postdata['name-group']);
                        }

                        $groupDn = "cn=".$postdata['name-group'].",ou=groups,dc=$dn[0],dc=$dn[1]";

                        $entry=array();
                        $entry['objectClass'][0]="posixGroup";
                        $entry['cn']=$postdata['name-group'];
                        $entry['gidNumber']=uniqid();
                        if(isset($postdata['users'])){$entry['memberUid']=$postdata['users'];}


                        $res_add = ldap_add($ldapConn, $groupDn, $entry);
                        echo ldap_error($ldapConn);
                        ldap_unbind($ldapConn);

                        if($res_add){

                            $num2=$gidNumber+1;

                            $this->modifyEnvFile("GID_NUMBER", strval($num2));
                        }else{
                            echo "Ha ocurrido un error al añadir el usuario";
                        }

                        break;
                    case 'organizationalUnit':
                        if (($first = strstr($postdata['ou-name'], ' ', true)) !== false){
                            $postdata['ou-name']=strtolower($first);
                        }else{
                            $postdata['ou-name']=strtolower($postdata['ou-name']);
                        }
                       

                        $ouDn = "ou=".$postdata['ou-name'].",dc=$dn[0],dc=$dn[1]";

                        $entry = array();
                        $entry["objectClass"][0]="organizationalUnit";
                        $entry["objectClass"][1]="top";
                        $entry["ou"]=$postdata['ou-name'];
                        $entry["description"]=$postdata['descripcion-name'];

                        $res_add = ldap_add($ldapConn, $ouDn, $entry);
                        echo ldap_error($ldapConn);
                        ldap_unbind($ldapConn);


                        break;
                    case 'policies':
                        $stringLdif = $postdata['policy'];
                        $pos = strpos($stringLdif, "dn");
                        if($pos != false){
                            $output = substr($stringLdif, $pos);
                            if (preg_match('/dn:(.*)/', $output, $matches)) {
                                $dn_string = trim($matches[1]);
                                $res_add = ldap_add($ldapConn, $dn_string, $output);
                            }
                        }else{
                            echo "Debes añadir el dn";
                        }

                        break;
                    case 'devices':
                        if (($first = strstr($postdata['host-name'], ' ', true)) !== false){
                            $postdata['host-name']=strtolower($first);
                        }else{
                            $postdata['host-name']=strtolower($postdata['host-name']);
                        }

                        $hostAttr=array();
                        $hostAttr["objectClass"]=array("top", "device", "ipHost");
                        $hostAttr["cn"]=$postdata['host-name'];
                        $hostAttr["description"]=$postdata['description'];
                        $hostAttr["ipHostNumber"]=$postdata['ipHost'];

                        $res_add = ldap_add($ldapConn, "cn=".$postdata['host-name'].",ou=devices,dc=$dn[0],dc=$dn[1]", $hostAttr);
                        echo ldap_error($ldapConn);

                        break;
                }
                if($res_add){

                    echo "La ou se ha añadido correctamente";

                }else{
                    echo "Ha ocurrido un error al añadir el usuario";
                }
                
            }

        }

        public function save_file_to_server(string $userFirstname, $files){

            $route = "user_photos";
            $image = $files;
            if (!file_exists($route)){
                mkdir($route, 0777, true);
            }
            
            $routePhoto = "$route/".strtolower(str_replace(' ', '', $userFirstname)).uniqid().".jpg";
            move_uploaded_file($image, $routePhoto);

            return $routePhoto;
        }

        public function deleteDomain($adminDom, $password, $domain){
            $server = $this->dotenv["LDAP_SERVER_ROUTE"];
            $username = $this->dotenv["LDAP_ROOT_USER"];
            $password = $this->dotenv["LDAP_ROOT_PASSWORD"];
            $uidUser = $this->dotenv["UID_NUMBER"];
            $gidNumber = $this->dotenv["GID_NUMBER"];

            $conn = new Connection();
            if(!$conn->binding($username, $password)){
                die("No se ha podido eliminar el dominio");
            }

            $entry = $conn->search("ou=users,".$domain,  "(objectClass=posixAccount)", []);
            if($entry['count'] > 0){
                $domNum = $entry[0]["employeeNumber"][0];
                $conn->unbind();
            }else{
                die("Ha ocurrido un error!");
            }


            // Establecemos la conexión con el servidor remoto
            $conn = ssh2_connect($server, 22);

            // Autenticamos con el usuario y contraseña
            if (ssh2_auth_password($conn, $username, $password)) {

                $stream = ssh2_exec($conn, "bash /home/$username/system_files_exec/delete_domain.sh $password $domNum $domain");

                stream_set_blocking($stream, true);
                stream_get_contents($stream);
                
                // Añadir el nuevo arbol en el servidor LDAP
                ssh2_disconnect($conn);


            }
        }
    }

?>