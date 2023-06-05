<?php

namespace model;

class Zone
{
    private $nameZone;
    private $ttl;
    private $fileName;

    public function __construct(string $nameZone=null, int $ttl=0, string $fileName=null)
    {
        $this->nameZone=$nameZone;
        $this->ttl=$ttl;
        // Directorio de zonas de bind
        $this->fileName=$fileName;
    }

    public function addZone(){
        
        // Contenido de la nueva zona
        $zone_content = <<<ZONE
        ;
        ; BIND data file for local loopback interface
        ;
        \$TTL    $this->ttl
        @       IN      SOA     ns1.$this->nameZone. root.$this->nameZone. (
                                    2         ; Serial
                                604800         ; Refresh
                                86400         ; Retry
                                2419200         ; Expire
                                604800 )       ; Negative Cache TTL
        ;
                IN      NS      ns1.$this->nameZone.
                IN      NS      ns2.$this->nameZone.

        ns1.$this->nameZone. IN      A       192.168.21.47
        ns2.$this->nameZone. IN      A       192.168.21.107
        $this->nameZone.             IN      A       192.168.21.47
        www     IN      CNAME   ns1.$this->nameZone.
        mail    IN      MX  10   ns2.$this->nameZone.
        ZONE;

        // Añadir la nueva zona al archivo de configuración
        $config_content = file_get_contents("/etc/bind/named.conf.local");
        $config_content .= "\nzone \"$this->nameZone\" {\n  type master;\n  file \"$this->fileName/db.$this->nameZone\";\n  allow-transfer { 192.168.21.47; };\n  notify yes;\n};\n";
        file_put_contents("/etc/bind/named.conf.local", $config_content);

        // Crear el archivo de zona
        file_put_contents("$this->fileName/db.$this->nameZone", $zone_content);

        // Reiniciar el servicio de BIND9 para aplicar los cambios
        exec("service bind9 restart");
    }

    public function getZones(string $newZone){
        $conf_str = file_get_contents("/etc/bind/named.conf.local");
        preg_match_all('/zone "(.+)" {/i', $conf_str, $matches);
        $zonas = $matches[1];
        $response = true;
        if (in_array($newZone, $zonas)){
            $response = false;
        }
        return $response;
    }

    public function deleteZone(){
        $namedConfFile = '/etc/bind/named.conf.local';

        $namedConfContent = file_get_contents($namedConfFile);

        $pattern = '/zone\s+"'. preg_quote($_SESSION['domain'], '/') .'\s*{.*?};/s';
        $namedConfContent = preg_replace($pattern, '', $namedConfContent);

        file_put_contents($namedConfFile, $namedConfContent);

        exec("service bind9 restart");
        exec("rm -r /etc/bind/zones/db.".$_SESSION['domain']);
    }
}
