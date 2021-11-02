function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
}

function enableDropdownInputs() {
    const dropdownInputs = document.querySelectorAll("[data-js=enable-dropdown]")

    for (const dropdownInput of dropdownInputs) {
        const dropdownData = document.getElementById(`datalist-for-${dropdownInput.id}`)

        dropdownInput.onfocus = function () {
            dropdownData.style.display = 'block'
        }

        dropdownInput.onblur = function (e) {
            sleep(100).then(() => {
                dropdownData.style.display = 'none'
            })
        }

        dropdownInput.oninput = function () {
            const text = dropdownInput.value.toUpperCase()
            const longText = dropdownInput.innerHTML
            for (let option of dropdownData.options) {
                if (option.value.toUpperCase().indexOf(text) > -1
                    || option.value.toUpperCase().indexOf(longText) > -1) {
                    option.style.display = "block"
                } else {
                    option.style.display = "none"
                }
            }
        }

        for (const option of dropdownData.options) {
            option.onclick = function () {
                dropdownInput.value = option.value
                dropdownData.style.display = 'none'
            }
        }

        dropdownInput.oninput = function () {
            const text = dropdownInput.value.toUpperCase()
            for (let option of dropdownData.options) {
                if (option.value.toUpperCase().indexOf(text) > -1) {
                    option.style.display = "block"
                } else {
                    option.style.display = "none"
                }
            }
        }

        let currentFocus = -1
        dropdownInput.onkeydown = function (e) {
            if (e.keyCode === 40) {
                currentFocus++
                addActive(dropdownData.options)
            } else if (e.keyCode === 38) {
                currentFocus--
                addActive(dropdownData.options)
            } else if (e.keyCode === 13) {
                e.preventDefault()
                if (currentFocus > -1) {
                    if (dropdownData.options)
                        dropdownData.options[currentFocus].click()
                }
            }
        }

        function addActive(x) {
            if (!x) return false
            removeActive(x)
            if (currentFocus >= x.length) currentFocus = 0
            if (currentFocus < 0)
                currentFocus = (x.length - 1)
            x[currentFocus].classList.add("active")
        }

        function removeActive(x) {
            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("active")
            }
        }
    }
}

export const run = () => enableDropdownInputs()