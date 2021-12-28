/**
 * Shuffles array in place. ES6 version
 * @param {Array} a items An array containing the items.
 */
function shuffle(a) {
    for (let i = a.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
}

function objectToQueryString(toSerialize, prefix) {
    const keyValuePairs = [];
    Object.keys(toSerialize).forEach((attribute) => {
        if (Object.prototype.hasOwnProperty.call(toSerialize, attribute)) {
            const key = prefix ? `${prefix}[${attribute}]` : attribute;
            const value = toSerialize[attribute];
            const toBePushed =
                value !== null && typeof value === "object"
                    ? objectToQueryString(value, key)
                    : `${key}=${value}`;
            keyValuePairs.push(toBePushed);
        }
    });
    return keyValuePairs.join("&");
}

export {
    shuffle,
    objectToQueryString
};