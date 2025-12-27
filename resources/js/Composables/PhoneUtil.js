import intlTelInput from "intl-tel-input";

 function initPhone(id) {

    let input = document.querySelector("#" + id);

    let iti =  intlTelInput(input, {
        utilsScript: "//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.min.js",
    });

    let country = import.meta.env.VITE_COUNTRY_ISO_CODE || "ss";
    iti.setCountry(country);

}
export default initPhone;
