<?php

    namespace model;
    class Apache{

        private $directorio;
        private $rutas;
        private $domain;

        public function __construct(string $directorio = null){
            $this->directorio="/var/www/".$directorio;
            $this->rutas=array();
            $this->domain=$directorio;
        }

        public function configureVirtualHost(){

            $virtualHost = '<VirtualHost *:80>
    RewriteEngine on
    RewriteCond %{HTTPS} !=on
    RewriteRule ^/?(.*) https://www.'.$this->domain.'/$1 [R=301,L]
</VirtualHost>
<VirtualHost *:443>

    ServerAdmin webmaster@localhost
    ServerName '.$this->domain.'
    ServerAlias www.'.$this->domain.'
    DocumentRoot '.$this->directorio.'

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    SSLEngine on
    SSLCertificateFile      /etc/apache2/certs/apache.crt
    SSLCertificateKeyFile   /etc/apache2/certs/apache.key

</VirtualHost>';

            $filename = "/etc/apache2/sites-available/".$this->domain.".conf";
            
            if(file_put_contents($filename, $virtualHost) !== false){
                shell_exec("a2ensite ".$this->domain.".conf");
                shell_exec("/etc/init.d/apache2 reload");
                return true;
            }else{
                return false;
            }
        }

        public function createStructure(){
            if(!is_dir($this->directorio)){
                if(mkdir($this->directorio, 0755, true)){
                    file_put_contents($this->directorio."/index.php", "<?php phpinfo(); ?>");
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

        public function getDirectorio(){
            return $this->directorio;
        }

        public function getRutas(){
            return $this->rutas;
        }

        public function listarCarpetas($ruta)
        {
            $output = shell_exec("tree -ifn $ruta");
            
            $this->formatRoutes($output);

            $routes = explode("\n", "<pre>$output</pre>");

            if(count($routes) >= 3){
                $routes = array_slice($routes, 0, count($routes) - 3);
            }

            return $routes;
        }

        public function formatRoutes($output){
            $lines = explode(PHP_EOL, $output);

            // Recorrer las líneas y extraer las rutas
            foreach ($lines as $line) {
                // Omitir líneas vacías o de encabezado
                if (empty($line) || strpos($line, 'directories') !== false || strpos($line, 'files') !== false) {
                    continue;
                }
                
                // Extraer la ruta del archivo o directorio
                $ruta = trim(preg_replace('/[^a-zA-Z0-9\s\/\.\_]/', '', $line));
                $this->rutas[] = $ruta;
            }

        }
        
        public function updateFile($directorio, $archivos){
            $response = true;
            if (!empty($archivos['name'][0])) {
                // Recorrer todos los archivos seleccionados
                for ($i = 0; $i < count($archivos['name']); $i++) {
                    $nombreArchivo = $archivos['name'][$i];
                    $archivoTemp = $archivos['tmp_name'][$i];
        
                    // Verificar si el archivo existe
                    if (file_exists($directorio . $nombreArchivo)) {
                        // Si el archivo existe, reemplazarlo con el nuevo archivo
                        if (move_uploaded_file($archivoTemp, $directorio . $nombreArchivo)) {
                            $response = true;
                        } else {
                            $response = false;
                        }
                    } else {
                        // Si el archivo no existe, guardar el nuevo archivo en la ruta definida
                        if (move_uploaded_file($archivoTemp, $directorio . $nombreArchivo)) {
                            $response = true;
                        } else {
                            $response = false;
                        }
                    }
                }
            } else {
                $response = false;
            }
        }

    }

?>