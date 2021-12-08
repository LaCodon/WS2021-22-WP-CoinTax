"use strict"

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

export const showInputError = (elem, message) => showError(elem, message)
export const clearInputError = (elem, message) => clearError(elem, message)