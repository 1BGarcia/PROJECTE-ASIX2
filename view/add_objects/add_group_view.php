<div class="add-form">
    <form action="index.php?controller=users&action=add_object_to_domain&control-action=groups" method="POST" enctype="multipart/form-data">
        <h1>Añadir un grupo</h1>
        <div class="form_data">
            <p>Nombre de grupo</p>
            <input type="text" name="name-group" require>
        </div>
        <div class="form_data">
            <p>Añadir usuarios al grupo.</p>
            <select name="users[]" id="" multiple>
                <?php

                foreach($_SESSION['users'] as $value){
                    echo "<option value='".$value->getUid()."'>".$value->getCn()."</option>";
                }

                ?>
            </select>
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>