import { defineStore } from "pinia";

export const useCurrencyStore = defineStore("currencyStore",{
    state:()=>({
        amountsObject:{}
    }),
    actions:{
         updateAmountStoreAction:async(data)=>{
            if(typeof data === 'object'){
                Object.assign(this.amountsObject,data)
            }
         },
         removeItemAmountStoreAction:async(key)=>{
            delete this.amountsObject[key];
         }
    }
})
