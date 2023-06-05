<div class="add-form">
    <form action="index.php?controller=users&action=add_object_to_domain&control-action=devices" method="POST" enctype="multipart/form-data">
        <h1>Añadir dispositivos</h1>
        <div class="form_data">
            <p>Nombre del dispositivo</p>
            <input type="text" name="host-name" require>
        </div>
        <div class="form_data">
            <p>Descripción</p>
            <input type="text" name="description" require>
        </div>
        <div class="form_data">
            <p>Dirección IP</p>
            <input type="text" name="ipHost" require>
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>