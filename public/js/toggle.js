"use strict"

function enableToggles() {
    const hideToggles = document.querySelectorAll("[data-hide='true']")
    for (const hideToggle of hideToggles) {
        hideToggle.style.display = 'none'
    }

    const toggleButtons = document.querySelectorAll("[data-toggle]")
    for (const toggleBtn of toggleButtons) {
        toggleBtn.onclick = function () {
            toggle(toggleBtn)
        }
    }
}

function toggle(toggleBtn) {
    const toggleId = toggleBtn.getAttribute('data-toggle')
    const toggleElement = document.getElementById(toggleId)
    const oldDisplay = toggleElement.style.display
    const span = toggleBtn.getElementsByTagName('span')[0]

    if (oldDisplay === 'none') {
        toggleElement.style.display = 'block'
        span.innerText = 'arrow_drop_up'
    } else {
        toggleElement.style.display = 'none'
        span.innerText = 'arrow_drop_down'
    }
}

export const run = () => enableToggles()
export const toggleAction = () => toggle()