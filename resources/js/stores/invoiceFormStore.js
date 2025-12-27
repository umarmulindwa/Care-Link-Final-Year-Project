import { defineStore } from "pinia";

export const useInvoiceFormStore = defineStore("invoiceFormStore",{
    state:()=>({
        EFRISRecieptFileState:null,
        EFRISRecieptInfoState:null,
        invoiceFileState:null,
        supportingFilesState:{},
        uploadFilesState:[],
        invoiceFilesState:{},
        focalPersonals:{},
        invoiceFormDataState:null,
        invoiceDetailsState:null,
    }),
    getters:{},
    actions:{
        async saveInvoiceFormDataState({formDetails,formData}){
            this.invoiceFormDataState = formData;
            this.invoiceDetailsState = formDetails;
        },
        async resetInvoiceFormDataState(){
            this.invoiceFormDataState = null;
            this.invoiceDetailsState = null;
        },
        async storeUploadedFiles(data){
            this.uploadFilesState = data
        },
        async storeEFRISReciept(data){
            this.EFRISRecieptFileState = data;
        },

        async removeEFRISReciept(){
            this.EFRISRecieptFileState = null;
        },
        async storeEFRISInfo(data){
            this.EFRISRecieptInfoState = data;
        },
        async clearEFRISInfo(){
            this.EFRISRecieptInfoState = null;
        },

        async storeInvoiceFile(data){
            this.invoiceFileState =  data;
        },
        async removeInvoiceFile(){
            this.invoiceFileState = null;
        },
        async storeSupportingFile(data){
             this.supportingFilesState[`${data.uuid}`] = data;
        },
        async removeSupportingFile(data){
            delete this.supportingFilesState[`${data.uuid}`];
        },
        async storeFocalPersonal(data){
                this.focalPersonals[`${data.uuid}`] = data;
        },
        async removeFocalPersonal(data){
            delete this.focalPersonals[`${data.uuid}`];
        },
    }
})
