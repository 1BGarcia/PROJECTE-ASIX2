const form = document.getElementById("webserv-panel")
var checkboxes = form.querySelectorAll("input[type=checkbox]")
var selects = form.querySelectorAll("select")
var inputFile = form.querySelectorAll("input[type=file]")[0]
var inputText = form.querySelectorAll("input[type=text]")[0]

console.log(inputFile[0])
// var formInputs = form.

inputFile.addEventListener("change", ()=>{
    if(inputFile.files.length != 0){
        inputText.disabled=true
    }else{
        inputText.disabled=false
    }
})

inputText.addEventListener("keyup", (e)=>{
    if(e.target.value != ""){
        inputFile.disabled=true
    }else{
        inputFile.disabled=false
    }
})



checkboxes.forEach(element => {
    element.addEventListener('click', (event)=>{
        if(element.checked){
            checkboxes.forEach(e => {
                if(e.name != event.target.name){
                    e.disabled=true
                }
            })
            if(element.name == "isCreate"){
                inputFile.disabled=true
            }
            
            if(element.name == "isDeleteDir" || element.name == "isCreate"){
                selects[1].style.display="none"
                selects[0].style.display="block"
            }else if(element.name == "isDeleteFile"){
                selects[0].style.display="none"
                selects[1].style.display="block"
            }

        }else{
            checkboxes.forEach(e => {
                if(e.name != event.target.name){
                    e.disabled=false
                }
            })
        }
    })
});
console.log(checkboxes)