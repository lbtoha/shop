"use strict";

import moment from "moment";

export function setQueryValue(key, value) {
    let url = new URL(window.location.href);
    url.searchParams.set(key, value);
    window.history.pushState({}, "", url);
}

export function getQueryParamsValue(key) {
    let url = new URL(window.location.href);
    return url.searchParams.get(key);
}

export function removeQueryParams(key) {
    if (Array.isArray(key)) {
        key.forEach((k) => {
            removeQueryParams(k);
        });
        return;
    }
    let url = new URL(window.location.href);
    url.searchParams.delete(key);
    window.history.pushState({}, "", url);
}

export function extractLatLngFromIframe(iframeHtml) {
    try {
        // Extract the src attribute from the iframe
        const srcMatch = iframeHtml.match(/src="([^"]+)"/);
        if (!srcMatch || !srcMatch[1]) return null;

        const url = srcMatch[1];
        const params = new URLSearchParams(url.split("?")[1]);
        const pb = params.get("pb");
        if (!pb) return null;

        // Split the pb parameter into parts
        const pbParts = pb.split("!");

        let latitude = null;
        let longitude = null;

        // Look for latitude (!3d) and longitude (!2d) in the !1m3 section
        for (const part of pbParts) {
            if (part.startsWith("2d")) {
                longitude = parseFloat(part.substring(2));
            } else if (part.startsWith("3d")) {
                latitude = parseFloat(part.substring(2));
            }
        }

        if (latitude === null || longitude === null) return null;

        return { latitude, longitude };
    } catch (error) {
        console.error("Error parsing iframe URL:", error);
        return null;
    }
}

export function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        const context = this;
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(context, args);
        }, delay);
    };
}

export function trimString(str, maxLength) {
    if (str.length <= maxLength) return str;
    return str.slice(0, maxLength) + "...";
}

export function getArrayDates(start_date, end_date) {
    const dates = [];
    const startDate = moment(start_date);
    const endDate = moment(end_date);
    while (startDate.isSameOrBefore(endDate)) {
        dates.push(startDate.format("YYYY-MM-DD"));
        startDate.add(1, "day");
    }
    return dates;
}

export function decrypt(encryptedValue) {
    return atob(encryptedValue);
}

export function getUrlType() {
    const url = window.location.pathname;
    const admin = "admin";
    if (url.includes(admin)) {
        return "/" + admin;
    }

    return "";
}
