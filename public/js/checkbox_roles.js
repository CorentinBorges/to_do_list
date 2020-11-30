let checkUser = document.getElementById('user_roleUser');
let checkAdmin = document.getElementById('user_roleAdmin');


checkUser.addEventListener('click',checkChangeUser)
checkAdmin.addEventListener('click',checkChangeAdmin)

function checkChangeUser() {
    if (checkUser.getAttribute('checked') === 'checked' ) {
            this.setAttribute('checked','false');
            checkAdmin.setAttribute('class','isChecked' )
            checkAdmin.checked=true;

    }
    else if (checkUser.getAttribute('checked') === 'false') {
        this.setAttribute('checked', 'checked');
        checkAdmin.setAttribute('class','false');
        checkAdmin.checked=false;

    }
}

function checkChangeAdmin() {
    if (this.getAttribute('class') ==='isChecked') {
        this.setAttribute('class','false');
        checkUser.setAttribute('checked','checked' )

        checkUser.checked=true
    }
    else if(this.getAttribute('class') ==='false'){
        checkAdmin.setAttribute('class','isChecked' )
        checkUser.setAttribute('checked', 'false');
        checkUser.checked=false
    }
}