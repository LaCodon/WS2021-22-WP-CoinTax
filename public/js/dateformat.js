"use strict"

function padNumber(x) {
    if (x < 10) {
        return `0${x}`
    }

    return x
}

function dateFormat(dateStr) {
    let date = new Date(dateStr.replace(' ', 'T') + 'Z')
    return `${padNumber(date.getDate())}.${padNumber(date.getMonth() + 1)}.${date.getFullYear()} ${padNumber(date.getHours())}:${padNumber(date.getMinutes())}`
}

export const formatDate = (dateStr) => dateFormat(dateStr)