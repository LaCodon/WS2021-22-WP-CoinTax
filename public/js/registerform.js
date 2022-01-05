import {showInputError, clearInputError} from "./inputError.js";

const form = document.getElementsByTagName('form')[0]

form.addEventListener('submit', function (e) {
    e.preventDefault()

    const firstname = form.elements['firstname'].value
    const lastname = form.elements['lastname'].value
    const email = form.elements['email'].value
    const password = form.elements['password'].value
    const passwordRepeat = form.elements['password-repeat'].value

    // check if passwords are equal, all other fields already get validated by formvalidation.js
    if (password !== passwordRepeat) {
        showInputError(form.elements['password'].parentElement, 'Die Passwörter müssen identisch sein')
        return
    }

    if (password.length < 5) {
        showInputError(form.elements['password'].parentElement, 'Das Passwort muss mindestens fünf Zeichen lang sein und einen Buchstaben und eine Zahl enthalten')
        return
    }

    const formData = new FormData()
    formData.append('firstname', firstname)
    formData.append('lastname', lastname)
    formData.append('email', email)
    formData.append('password', password)
    formData.append('password-repeat', passwordRepeat)

    fetch('../api/register', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
        .then(result => {
            if (result.result === 'success') {
                window.location = '../login/'
            } else if (result.result === 'email_taken') {
                showInputError(form.elements['email'].parentElement, 'Die Adresse wird bereits von einem anderen User verwendet');
            } else if (result.result === 'invalid_input') {
                for (const field in result.validation) {
                    showInputError(form.elements[field].parentElement, result.validation[field])
                }
            }
        })
        .catch(error => {
            console.log(error)
            alert('Fehler beim Registrieren, versuchen Sie es später erneut.')
        });
})