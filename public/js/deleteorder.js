function deleteOrderAction() {
    const deleteButtons = document.querySelectorAll('[data-delete-order]')
    for (const deleteBtn of deleteButtons) {

        deleteBtn.parentElement.onclick = function (e) {
            // prevent from following a.href
            e.preventDefault()
        }

        deleteBtn.onclick = function () {
            if (confirm('Soll diese Order wirklich gelöscht werden?')) {
                const orderId = deleteBtn.getAttribute('data-delete-order')

                const xhr = new XMLHttpRequest()
                xhr.open('GET', './delete.do?xhr=1&id=' + orderId)

                xhr.onerror = function () {
                    alert('Unerwarteter Fehler beim löschen der Order')
                }

                xhr.onload = function () {
                    console.log(xhr.status)
                    if (xhr.status === 200) {
                        if (deleteBtn.getAttribute('data-closetab') === 'true') {
                            window.close();
                        } else {
                            const parentOrder = document.getElementById('order-' + orderId)
                            parentOrder.remove()
                        }
                    } else {
                        xhr.onerror(undefined)
                    }
                }

                xhr.send()
            }
        }

    }
}

export const run = () => deleteOrderAction()