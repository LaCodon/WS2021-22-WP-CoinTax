"use strict"

import {showInputError, clearInputError} from "./inputError.js";

function formValidation() {
    const forms = document.getElementsByTagName('form')

    validateRequiredInputs()
    validatePatternInputs()

    for (const form of forms) {
        const inputs = form.elements;

        form.errorCount = 0

        for (const input of inputs) {
            input.addEventListener('blur', function () {
                if (input.classList.contains('error')) {
                    if (input.wasErrornous === undefined || input.wasErrornous === false) {
                        form.errorCount++
                        input.wasErrornous = true
                    }
                } else {
                    if (input.wasErrornous !== undefined && input.wasErrornous === true) {
                        form.errorCount -= form.errorCount > 0 ? 1 : 0
                        input.wasErrornous = false
                    }
                }
            })
        }

        form.addEventListener('submit', function (e) {
            if (form.errorCount !== 0)
                e.preventDefault()
        })
    }
}

function validatePatternInputs() {
    const patternInputs = document.querySelectorAll('input[pattern]')

    for (const input of patternInputs) {
        input.addEventListener('blur', function (e) {
            const regexPattern = input.getAttribute('pattern')
            if (regexPattern !== null) {
                const match = input.value.match(regexPattern)
                if (match === null || match[0] !== input.value) {
                    showInputError(input.parentElement, 'Der eingegebene Wert hat das falsche Format')
                } else {
                    clearInputError(input.parentElement)
                }
            }
        })
    }
}

function validateRequiredInputs() {
    const requiredInputs = document.querySelectorAll('input[required]')

    for (const input of requiredInputs) {
        if (input.getAttribute('data-js') === 'enable-dropdown') {
            continue
        }

        input.addEventListener('blur', function (e) {
            if (input.value === '') {
                showInputError(input.parentElement, 'Dieses Feld muss ausgefüllt sein.')
                e.stopImmediatePropagation()
            } else {
                clearInputError(input.parentElement)
            }
        })
    }
}

export const run = () => formValidation()