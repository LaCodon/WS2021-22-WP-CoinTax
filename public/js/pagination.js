"use strict"

import {run as enableToggles} from './toggle.js'
import {run as enableDeleteOrderBtns} from './deleteorder.js'
import {formatNumber} from './numberformat.js'
import {formatDate} from './dateformat.js'

function enableAjaxPagination() {
    ajaxPagination.paginator = document.querySelector('[data-js=enable-ajax-pagination]')
    ajaxPagination.loader = document.getElementById('card-loading')
    ajaxPagination.enabled = true

    if (ajaxPagination.paginator === null || ajaxPagination.loader === null) return

    ajaxPagination.currentPage = ajaxPagination.paginator.getAttribute('data-js-page')
    ajaxPagination.maxPage = ajaxPagination.paginator.getAttribute('data-js-maxpage')
    ajaxPagination.filter = ajaxPagination.paginator.getAttribute('data-js-filter')
    ajaxPagination.endpoint = ajaxPagination.paginator.getAttribute('data-ajax-endpoint')

    window.onscroll = ajaxPagination
}

function ajaxPagination() {
    if (!ajaxPagination.enabled) return

    // true if bottom paginator scrolls into viewport
    const shouldFetch = window.innerHeight - ajaxPagination.paginator.getBoundingClientRect().top - 200 >= 0
    if (!shouldFetch) return
    if (++ajaxPagination.currentPage > ajaxPagination.maxPage) return

    console.log('fetching more list items...')

    // the following code only runs if there is no other current fetch
    ajaxPagination.enabled = false
    ajaxPagination.loader.style.display = 'block'

    const xhr = new XMLHttpRequest()
    xhr.open('GET', `${ajaxPagination.endpoint}?${ajaxPagination.filter}&page=${ajaxPagination.currentPage}`)

    xhr.onerror = function (e) {
        console.log(e)
    }

    xhr.onload = function () {
        if (xhr.status === 200) {
            if (xhr.response === [])
                return

            printPaginator()

            const result = JSON.parse(xhr.response)
            for (const order of result) {
                if (ajaxPagination.endpoint.includes('orders') === true)
                    printOrder(order.orderId, order)
                else if (ajaxPagination.endpoint.includes('transactions') === true)
                    printTransactions(order.orderId, order)
            }

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

function printOrder(id, order) {
    let feeHtml = ''
    if (order.fee !== null) {
        feeHtml = `<td>Gebühren</td>
            <td>${order.feeCoin.symbol}</td>
            <td>${formatNumber(order.fee.value)}</td>
            <td>${formatNumber(order.feeValue, 2)} EUR</td>`
    }

    let date = formatDate(order.base.datetimeUtc.date)

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

function printTransactions(id, order) {
    let date = ''
    if (order.base !== null)
        date = formatDate(order.base.datetimeUtc.date)
    else if (order.quote !== null)
        date = formatDate(order.quote.datetimeUtc.date)
    else
        date = formatDate(order.fee.datetimeUtc.date)

    if (order.fee !== null) {
        const feeNode = document.createElement('div')
        feeNode.setAttribute('class', 'w12 flexbox card')
        feeNode.innerHTML += makeTransactionHtml(id, date, order.fee, order.feeCoin, order.feeValue, true)
        document.getElementById('ajax-content').appendChild(feeNode)
    }

    if (order.quote !== null) {
        const quoteNode = document.createElement('div')
        quoteNode.setAttribute('class', 'w12 flexbox card')
        quoteNode.innerHTML += makeTransactionHtml(id, date, order.quote, order.quoteCoin, order.fiatValue)
        document.getElementById('ajax-content').appendChild(quoteNode)
    }

    if (order.base !== null) {
        const baseNode = document.createElement('div')
        baseNode.setAttribute('class', 'w12 flexbox card')
        baseNode.innerHTML += makeTransactionHtml(id, date, order.base, order.baseCoin, order.fiatValue)
        document.getElementById('ajax-content').appendChild(baseNode)
    }

    const separatorNode = document.createElement('div')
    separatorNode.setAttribute('class', 'm01')
    document.getElementById('ajax-content').appendChild(separatorNode)
}

function makeTransactionHtml(orderId, date, transaction, coin, value, isFee = false) {
    return `<div class="flexbox w2 flex-col flex-gap">
            <a href="../order/details?id=${orderId}">
                <span class="material-icons swap-icon ${transaction.type === 'send' ? 'red' : 'green'}">${transaction.type === 'send' ? 'arrow_upward' : 'arrow_downward'}</span>
            </a>
            <span class="text-light">${date} Uhr</span>
        </div>

        <div class="flexbox w8 flexbox-center">
            <div class="flexbox flexbox-center flex-col w2 flex-gap">
                <div><img class="token-symbol"
                          src="${coin.thumbnailurl}"
                          alt="${coin.name}"></div>
                <div class="text-light text-center">
                    ${formatNumber(transaction.value, 8)}
                    ${coin.symbol}<br>
                    ${isFee ? '<span class="hint">Gebühr</span>' : ''} 
                </div>
            </div>
        </div>

        <div class="w2">
            <div class="text-light">
                Wert: ${formatNumber(value, 2)} EUR
            </div>
        </div>`
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