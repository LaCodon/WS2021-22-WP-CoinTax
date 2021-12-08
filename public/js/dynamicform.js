"use strict"

import {showInputError, clearInputError} from "./inputError.js";

function enableDynamicForm() {
    const form = document.getElementById('js-dynamic-form')
    if (form === null)
        return;

    const nextBtn = document.getElementById('js-next-btn')
    const submitBtn = document.querySelector('button[type=submit]')
    const formElems = form.getElementsByClassName('form-elem')

    const elemCount = formElems.length
    let key = 0
    let currentActiveElement = 0

    for (const formElem of formElems) {
        if (key++ !== 0)
            formElem.classList.add('hide')
    }

    nextBtn.classList.remove('hide')
    nextBtn.onclick = function (e) {
        e.preventDefault()

        const currentInput = formElems[currentActiveElement].getElementsByTagName('input')[0]

        if (currentInput.value === '') {
            showInputError(formElems[currentActiveElement], 'Das Feld darf nicht leer sein')
            return
        }

        const regexPattern = currentInput.getAttribute('pattern')
        if (regexPattern !== null) {
            const match = currentInput.value.match(regexPattern)
            if (match === null || match[0] !== currentInput.value) {
                showInputError(formElems[currentActiveElement], 'Der eingegebene Wert hat das falsche Format')
                return
            }
        }

        if (currentInput.getAttribute('type') === 'checkbox') {
            if (currentInput.checked !== true) {
                showInputError(formElems[currentActiveElement], 'Bestätigen Sie durch setzen des Hakens')
                return
            }
        }

        clearInputError(formElems[currentActiveElement])
        currentInput.disabled = true
        currentActiveElement++
        formElems[currentActiveElement].classList.remove('hide')

        if (currentActiveElement === elemCount - 1) {
            nextBtn.classList.add('hide')
        }
    }

    submitBtn.onclick = function (e) {
        e.preventDefault()
        for (const formElem of formElems) {
            const inputs = formElem.getElementsByTagName('input')
            if (inputs.length > 0)
                inputs[0].disabled = false;
        }
        form.submit()
    }
}

/*
function showError(elem, message) {
    const span = elem.getElementsByTagName('span')[0]
    const input = elem.getElementsByTagName('input')[0]

    input.classList.add('error')
    span.classList.add('error')
    span.innerHTML = message
}

function clearError(elem) {
    const span = elem.getElementsByTagName('span')[0]
    const input = elem.getElementsByTagName('input')[0]

    input.classList.remove('error')
    span.classList.remove('error')
    span.innerHTML = ''
}
*/

export const run = () => enableDynamicForm()