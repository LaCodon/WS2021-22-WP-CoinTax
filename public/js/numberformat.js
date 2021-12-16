"use strict"

// numberFormat does not round correctly! it should only be used for simple displaying purposes
function numberFormat(numberStr, decimalCount = 8) {
    const parts = numberStr.split('.')
    let decimals = parts[1].slice(0, Math.min(decimalCount, parts[1].length))

    decimals = decimals.replace(new RegExp("[0]+$"), "")

    if (decimals.length === 1) {
        decimals += '0'
    }

    if (decimals === '') {
        decimals = '00'
    }

    return `${parts[0]},${decimals}`
}

export const formatNumber = (numberStr, decimalCount) => numberFormat(numberStr, decimalCount)