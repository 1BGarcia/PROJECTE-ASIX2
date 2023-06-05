const expresiones = {
    dominio: /^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)$/,
    telefono: /^\d{9}$/,
    contrasena: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-_])[A-Za-z\d_-]{6,}$/
}


const validateForm = (event) => {
    event.preventDefault()
    const FORM_REGISTER = document.getElementById("signup_form")
    var inputs_register = document.querySelectorAll("#signup_form .form_data input")
    console.log(inputs_register)

    inputs_register.forEach(input=>{
        if(input.name != "reset" && input.name != "submit"){
            input.addEventListener("keyup", validar)
        }
    })
    
}

const validar=(event)=>{
    if(event.target.value != ""){
        switch(event.target.name){
            case "domain":
                if(expresiones.dominio.test(e.target.value)){
                    console.log("hola")
                }
    
            break;
            // case "domain":
                
    
            // break;
            // case "domain":
                
    
            // break;
            // case "domain":
                
    
            // break;
        }
    }else{
        alert("No pueden haber campos vacios")
    }
}

// function isValid(){
//     var response = false
//     var inputs = document.querySelectorAll("#signup_form input")

//     var password = ""


//     inputs.forEach(input =>{
//         console.log(input.value, input.name)
//         if(input.name != "reset" && input.name != "submit"){
//             if(input.value != ""){
//                 switch(input.name){
//                     case 'domain':
//                         if(/^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)$/.test(input.value)){
//                             response = true
//                         }else{
//                             response = false
//                             alert("El nombre de dominio introducido es incorrecto. Ejemplo: domain.com")
//                         }
//                         break
//                     case 'tel':
//                         if(/^\d{9}$/.test(input.value)){
//                             response = true
//                         }else{
//                             response = false
//                             alert("El numero de telefono debe contener 9 digitos")
//                         }
//                         break
//                     case 'password':
//                         if(/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-_])[A-Za-z\d_-]{6,}$/
//                         .test(input.value)){
//                             response = true
//                             password = input.value
//                             console.log(password)
//                         }else{
//                             response = false
//                             alert("La contraseña debe tener minimo 6 caracteres, minusculas, mayusculas y numeros")
//                         }
//                         break
//                     case 'rep_password':
//                         if(input.value == password){
//                             response = true
//                         }else{
//                             response = false
//                             alert("Las contraseñas no coinciden")
//                         }
//                         break
//                     case 'photo':
//                         const file = input.files[0];
//                         const reader = new FileReader();
    
//                         if (file && /\.(jpg)$/i.test(file.name)) {
//                             reader.readAsDataURL(file);
//                             reader.onload = () => {
//                             const img = new Image();
//                             img.src = reader.result;
//                                 img.onload = () => {
//                                     if (img.width === 250 && img.height === 250) {
//                                         console.log("test")
//                                         response = true
//                                     } else {
//                                         response = false
//                                         alert("La imagen debe tener dimensiones de 250x250");
//                                         input.value = "";
//                                     }
//                                 }
//                             }
//                         } else {
//                             response = false
//                             alert("Debe seleccionar una imagen en formato PNG o JPG");
//                             input.value = "";
//                         }
                        
//                         break
//                 }
//             }else{
//                 response = false
//             }
//         }
//     })
//     console.log(response)

//     return response
// }

// const data = [
//     { name: "Manzana", description: "Una fruta deliciosa." },
//     { name: "Banana", description: "Una fruta tropical amarilla." },
//     { name: "Naranja", description: "Una fruta cítrica." },
//     { name: "Lechuga", description: "Una verdura fresca para ensaladas." },
//     { name: "Tomate", description: "Una fruta roja utilizada en ensaladas y salsas." },
//     { name: "Pera", description: "Una fruta dulce y jugosa." }
// ];

// function search(){
//     const input = document.getElementById("search-input");
//     const query = input.value.toLowerCase();
//     const results = data.filter(item => item.name.toLowerCase().includes(query));
//     const list = document.getElementById("results-list");
//     list.innerHTML = "";
//     if (input.value != ""){
//         list.style.display = "block";
//         results.forEach(item => {
//             const li = document.createElement("li");
//             li.textContent = `${item.name}: ${item.description}`;
//             list.appendChild(li);
//         });
//     }else{
//         list.style.display = "none";
//         list.innerHTML = "";
//     }
// }

// 
// var directoryTree = document.getElementById('directoryTree');

// // Agregar manejadores de eventos para expandir y contraer los directorios
// directoryTree.addEventListener('click', function (event) {
//     if (event.target.tagName === 'SPAN') {
//     event.target.parentElement.classList.toggle('expanded');
//     }
// });
