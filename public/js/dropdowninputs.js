"use strict"

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
}

function getParent(el, type) {
    type = type.toUpperCase()
    let parent = el.parentNode

    while (parent !== document) {
        if (parent.tagName === type)
            return parent
        parent = parent.parentNode
    }

    return undefined
}

function enableDropdownInputs() {
    const dropdownInputs = document.querySelectorAll("[data-js=enable-dropdown]")

    for (const dropdownInput of dropdownInputs) {
        const dropdownData = document.getElementById(`datalist-for-${dropdownInput.id}`)

        // disable browser default list
        dropdownInput.setAttribute('list', '')

        dropdownInput.onfocus = function () {
            dropdownData.style.display = 'block'
        }

        dropdownInput.addEventListener('blur', function (e) {
            sleep(100).then(() => {
                dropdownData.style.display = 'none'
            })
        })

        dropdownData.addEventListener('click', function (e) {
            if (e.target.tagName !== 'OPTION') {
                dropdownInput.value = getParent(e.target, 'option').value
                dropdownData.style.display = 'none'
            } else {
                dropdownInput.value = e.target.value
                dropdownData.style.display = 'none'
            }
        })

        let currentFocus = -1
        dropdownInput.onkeydown = function (e) {
            if (e.keyCode === 40) {
                selectNextActive(dropdownData.options, +1)
            } else if (e.keyCode === 38) {
                selectNextActive(dropdownData.options, -1)
            } else if (e.keyCode === 13) {
                e.preventDefault()
                if (currentFocus > -1) {
                    if (dropdownData.options)
                        dropdownData.options[currentFocus].click()
                }
            }
        }

        function selectNextActive(x, direction) {
            if (!x) return false

            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("active")
            }

            for (let i = 0; i < x.length * 2; ++i) {
                currentFocus += direction
                currentFocus = Math.abs(((currentFocus % x.length) + x.length) % x.length)
                if (x[currentFocus].style.display === 'block' || x[currentFocus].style.display === '') break
            }

            x[currentFocus].classList.add("active")
        }
    }
}

export const run = () => enableDropdownInputs()