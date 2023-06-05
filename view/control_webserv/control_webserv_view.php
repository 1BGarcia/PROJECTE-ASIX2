<div class="control_web_sup">
<div class="control_web">
    <?php
    echo "<h3>Administración Web</h3>";
    echo "<br>";
    $array = $apache->listarCarpetas($apache->getDirectorio());
    
    echo '<ul class="tree" id="directoryTree">';

    foreach ($array as $ruta) {
        $rutaParts = explode('/', $ruta);
        $nivel = count($rutaParts) - 1;
        $nombre = end($rutaParts);
        $indentation = str_repeat("&nbsp;", $nivel * 4);

        echo '<li><span class="folder">' . $nombre . '</span>';

        // Verificar si es un directorio
        if (is_dir($ruta)) {
            echo '<ul>';

            // Obtener los contenidos del directorio
            $contenido = scandir($ruta);

            // Filtrar los elementos "." y ".."
            $contenido = array_diff($contenido, [".", ".."]);

            foreach ($contenido as $elemento) {
                $elementoRuta = $ruta . '/' . $elemento;

                // Verificar si es un directorio
                if (is_dir($elementoRuta)) {
                    echo '<li><span class="folder">' . $elemento . '</span></li>';
                } else {
                    echo '<li><span class="file">' . $elemento . '</span></li>';
                }
            }

            echo '</ul>';
        }

        echo '</li>';
    }

    echo '</ul>';

    ?>

    <form action="index.php?controller=control_webserv_panel&action=view_webserv_panel" method="POST" enctype="multipart/form-data" id="webserv-panel">
        <div class="form_data">
            <p>Selecciona esta opcion si quieres crear un directorio</p>
            <input type="checkbox" name="isCreate">
        </div>
        <div class="form_data">
            <p>Selecciona esta opcion si quieres eliminar un directorio</p>
            <input type="checkbox" name="isDeleteDir">
        </div>
        <div class="form_data">
            <p>Selecciona esta opcion si quieres eliminar fichero</p>
            <input type="checkbox" name="isDeleteFile">
        </div>
        <div class="form_data">
            <select name="routesDir" style="display:block;">
                <option value="" selected>Selecciona la ruta del directorio</option>
                <?php

                    for($i=0; $i<count($apache->getRutas()); $i++){
                        if(is_dir($apache->getRutas()[$i])){
                            echo "<option value='".$apache->getRutas()[$i]."'>".$apache->getRutas()[$i]."</option>";
                        }
                    }

                ?>
                
            </select>
            <select name="routesFile" style="display: none;">
                <option value="" selected>Selecciona la ruta del archivo</option>
                <?php

                    for($i=0; $i<count($apache->getRutas()); $i++){
                        if(is_file($apache->getRutas()[$i])){
                            echo "<option value='".$apache->getRutas()[$i]."'>".$apache->getRutas()[$i]."</option>";
                        }
                    }

                ?>
                
            </select>
        </div>
        <div class="form_data">
            <p>Introduce el nuevo nombre del directorio</p>
            <input type="text" name="newName">
        </div>
        <div class="form_data">
            <p>Añadir el fichero</p>
            <input type="file" name="filesGet[]" multiple>
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>
</div>