export function secondsToHMS(secs) {
    const hms = {};

    hms.hours = Math.floor(secs / 3600);
    hms.minutes = Math.floor(secs / 60) % 60;
    hms.seconds = Math.floor(secs) % 60;

    return hms;
}

export function hmsToSeconds(hms) {
    return hms.hours * 3600 + hms.minutes * 60 + hms.seconds;
}

export function formatHMS(hms) {
    return hms.hours + ' jam ' + hms.minutes + ' menit ' + hms.seconds + ' detik'
}

