export function isEmailSent(email) {

    if (email == null) return "";

    const generationTime = moment(email.updated_at);

    const currentTime = moment();

    const differenceInHours = currentTime.diff(generationTime, "hours");

    if (email.is_sent == 1) {
        return "sent";
    } else if (email.is_sent == 0 && differenceInHours > 24) {
        return "not sent";
    } else if (email.is_sent == 0 && differenceInHours < 24) {
        return "sending";
    } else {
        return "";
    }
}
