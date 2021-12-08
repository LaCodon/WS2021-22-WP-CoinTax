"use strict"

import {showInputError, clearInputError} from "./inputError.js";

function loadCoinsAsync() {
    const dropdownInputs = document.querySelectorAll("[data-js=enable-dropdown]")

    for (const dropdownInput of dropdownInputs) {
        const dropdownData = document.getElementById(`datalist-for-${dropdownInput.id}`)

        dropdownInput.oninput = function () {
            const text = dropdownInput.value.toUpperCase()
            if (text === '')
                return

            clearInputError(dropdownInput.parentNode)

            // clear old results
            const optionCount = dropdownData.options.length
            for (let i = 0; i < optionCount; ++i) {
                dropdownData.removeChild(dropdownData.options[0])
            }

            // query server for coins
            const xhr = new XMLHttpRequest()
            xhr.open('GET', '../api/querycoins?query=' + text)

            xhr.onerror = function (e) {
                showInputError(dropdownInput.parentNode, 'Nur Buchstaben erlaubt')
            }

            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (xhr.response === [])
                        return

                    const result = JSON.parse(xhr.response)

                    Object.keys(result).forEach(key => {
                        const option = document.createElement('option')
                        option.value = key
                        option.innerHTML = `<div class="flexbox flex-start flex-center flex-gap">
                                <img class="token-symbol-small" src="${result[key].thumbnail}" alt="${key}">
                                <span>${result[key].name}</span>
                            </div>`
                        dropdownData.appendChild(option)
                    });
                } else {
                    xhr.onerror(undefined)
                }
            }

            xhr.send()
        }
    }
}

export const run = () => loadCoinsAsync()