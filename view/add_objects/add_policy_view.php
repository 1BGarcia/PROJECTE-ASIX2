<div class="add-form-policy">
    <form action="index.php?controller=users&action=add_object_to_domain&control-action=policies" method="POST" enctype="multipart/form-data">
        <h1>Añadir politica de seguridad</h1>
        <div class="form_data">
            <textarea name="policy" id="policy" cols="30" rows="20">
<-- EDITA ESTA PLANTILLA PARA AÑADIR LA POLITICA QUE PREFIERAS -->

dn: cn=MyOrgPPolicy,ou=policy,dc=tu_dominio,dc=tu_dominio
cn: MyOrgPPolicy
objectClass: pwdPolicy
objectClass: device
objectClass: top
pwdAttribute: nombre_de_la_politica
pwdMaxAge: 2592000
pwdExpireWarning: 600
pwdInHistory: 5
pwdCheckQuality: 2
pwdMinLength: 8
pwdMaxFailure: 5
pwdLockout: TRUE
pwdLockoutDuration: 1800
pwdGraceAuthNLimit: 0
pwdFailureCountInterval: 60</textarea>
        </div>
        <div class="form_data">
            <input type="submit" name="submit">
            <input type="reset" name="reset">
        </div>
    </form>
</div>