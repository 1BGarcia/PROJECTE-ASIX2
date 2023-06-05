<div class="add-form">
    <form action="index.php?controller=users&action=add_object_to_domain&control-action=organizationalUnit" method="POST" enctype="multipart/form-data">
        <h1>Añadir unidad organizativa</h1>
        <div class="form_data">
            <p>Nombre la unidad organizativa</p>
            <input type="text" name="ou-name" require>
        </div>
        <div class="form_data">
            <p>Descripción</p>
            <input type="text" name="descripcion-name" require>
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>