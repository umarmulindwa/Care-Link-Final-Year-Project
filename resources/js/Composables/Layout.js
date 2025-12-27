// import {reactive} from 'vue'

//sets default layout

export function setLayout(){
    // const config = reactive({
        
    //         type: "horizontal",
    //         sidebar: "light",
    //         width: "fluid",
    //         topbar: "light",
    //         mode: "light",
    //         loader: false
        
    // })
    //fluid size config
    document.body.setAttribute("data-layout-mode", "light");
    document.body.removeAttribute("data-layout-size");
    //store config to localstorage
    localStorage.setItem("layout", JSON.stringify({
        
        type: "horizontal",
        sidebar: "light",
        width: "fluid",
        topbar: "light",
        mode: "light",
        loader: false
    
}));

}