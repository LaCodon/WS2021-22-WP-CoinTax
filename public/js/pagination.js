"use strict"

import {run as enableToggles} from './toggle.js'
import {run as enableDeleteOrderBtns} from './deleteorder.js'
import {formatNumber} from './numberformat.js'

function enableAjaxPagination() {
    ajaxPagination.paginator = document.querySelector('[data-js=enable-ajax-pagination]')
    ajaxPagination.loader = document.getElementById('card-loading')
    ajaxPagination.enabled = true

    if (ajaxPagination.paginator === null || ajaxPagination.loader === null) return

    ajaxPagination.currentPage = ajaxPagination.paginator.getAttribute('data-js-page')
    ajaxPagination.maxPage = ajaxPagination.paginator.getAttribute('data-js-maxpage')
    ajaxPagination.filter = ajaxPagination.paginator.getAttribute('data-js-filter')

    window.onscroll = ajaxPagination
}

function ajaxPagination() {
    if (!ajaxPagination.enabled) return

    // true if bottom paginator scrolls into viewport
    const shouldFetch = window.innerHeight - ajaxPagination.paginator.getBoundingClientRect().top - 200 >= 0
    if (!shouldFetch) return
    if (++ajaxPagination.currentPage > ajaxPagination.maxPage) return

    console.log('fetching more trades...')

    // the following code only runs if there is no other current fetch
    ajaxPagination.enabled = false
    ajaxPagination.loader.style.display = 'block'

    const xhr = new XMLHttpRequest()
    xhr.open('GET', `../api/queryorders?${ajaxPagination.filter}&page=${ajaxPagination.currentPage}`)

    xhr.onerror = function (e) {
        console.log(e)
    }

    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.response === [])
                return

            printPaginator()

            const result = JSON.parse(xhr.response)
            Object.keys(result).forEach((key) => {
                printOrder(key, result[key])
            })

            enableToggles()
            enableDeleteOrderBtns()

            ajaxPagination.paginator.innerHTML = ''

            ajaxPagination.enabled = true
            ajaxPagination.loader.style.display = 'none'
        } else {
            xhr.onerror(undefined)
        }
    }

    xhr.send()
}

function padNumber(x) {
    if (x < 10) {
        return `0${x}`
    }

    return x
}

function printOrder(id, order) {
    let feeHtml = ''
    if (order.fee !== null) {
        feeHtml = `<td>Gebühren</td>
            <td>${order.feeCoin.symbol}</td>
            <td>${formatNumber(order.fee.value)}</td>
            <td>${formatNumber(order.feeValue, 2)} EUR</td>`
    }

    let date = new Date(order.base.datetimeUtc.date.replace(' ', 'T') + 'Z')
    date = `${padNumber(date.getDate())}.${padNumber(date.getMonth() + 1)}.${date.getFullYear()} ${padNumber(date.getHours())}:${padNumber(date.getMinutes())}`

    const orderNode = document.createElement('div')
    orderNode.setAttribute('id', 'order-' + id)
    orderNode.setAttribute('class', 'w12 flexbox card')

    orderNode.innerHTML = `<div class="flexbox w2 flex-col flex-gap">
            <span class="material-icons swap-icon">swap_horiz</span>
            <span class="text-light">${date} Uhr</span>
        </div>

        <div class="flexbox w8 flexbox-center">
            <div class="flexbox flexbox-center flex-col w2 flex-gap">
                <div><img class="token-symbol"
                          src="${order.baseCoin.thumbnailurl}"
                          alt="${order.baseCoin.name}"></div>
                <div class="text-light">
                    ${formatNumber(order.base.value)}
                    ${order.baseCoin.symbol}
                </div>
            </div>
            <div><span class="material-icons">chevron_right</span></div>
            <div class="flexbox flexbox-center flex-col w2 flex-gap">
                <div><img class="token-symbol"
                          src="${order.quoteCoin.thumbnailurl}"
                          alt="${order.quoteCoin.name}"></div>
                <div class="text-light">
                    ${formatNumber(order.quote.value)}
                    ${order.quoteCoin.symbol}
                </div>
            </div>
        </div>

        <div class="flexbox w2">
            <div></div>
            <div>
                <button class="loupe-btn no-btn" data-toggle="order-toggle-${id}">
                    <span class="material-icons loupe-icon text-light">arrow_drop_down</span>
                </button>
            </div>
        </div>

        <div id="order-toggle-${id}" data-hide="true" class="flexbox w12 swap-details" style="display: none">
            <div class="w12 flexbox flexbox-center">

                <div class="w6 container">

                    <table class="table">
                        <thead class="table-head">
                        <tr>
                            <th></th>
                            <th>Token</th>
                            <th>Menge</th>
                            <th>Wert</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <tr>
                            <td>Gesendet</td>
                            <td>${order.baseCoin.symbol}</td>
                            <td>${formatNumber(order.base.value)}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Empfangen</td>
                            <td>${order.quoteCoin.symbol}</td>
                            <td>${formatNumber(order.quote.value)}</td>
                            <td>${formatNumber(order.fiatValue, 2)} EUR</td>
                        </tr>
                        <tr>
                            ${feeHtml}
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="w1"></div>
                <div class="w2 flexbox flex-col flex-gap flex-end flex-stretch">
                    <a class="flexbox flex-col flex-stretch"
                       href="./details?id=${id}">
                        <button class="btn default flexbox flexbox-center flex-gap">
                            <span class="material-icons">zoom_in</span>
                            Details
                        </button>
                    </a>

                    <a class="flexbox flex-col flex-stretch"
                       href="./delete.do?id=${id}">
                        <button class="btn warning flexbox flexbox-center flex-gap"
                                data-delete-order="${id}">
                            <span class="material-icons">delete_outline</span>
                            Trade löschen
                        </button>
                    </a>

                </div>

            </div>
        </div>`

    document.getElementById('ajax-content').appendChild(orderNode)
}

function printPaginator() {
    const paginatorNode = document.createElement('div')
    paginatorNode.setAttribute('class', 'flexbox w12 m02 flex-center flex-top')
    paginatorNode.innerHTML = `<div class="paginator flexbox flex-stretch" data-js="enable-ajax-pagination"
            data-js-filter="${ajaxPagination.filter}" data-js-page="${ajaxPagination.currentPage}" data-js-maxpage="${ajaxPagination.maxPage}">
           <a href="./?${ajaxPagination.filter}" class="paginator-item text-light"><span class="material-icons">first_page</span></a>
           <a href="./?page=${ajaxPagination.currentPage}&${ajaxPagination.filter}" class="paginator-item text-light active">${ajaxPagination.currentPage}</a>
           <a href="./?page=${ajaxPagination.maxPage}&${ajaxPagination.filter}" class="paginator-item text-light"><span class="material-icons">last_page</span></a>
        </div>`

    document.getElementById('ajax-content').appendChild(paginatorNode)
}

export const run = () => enableAjaxPagination()