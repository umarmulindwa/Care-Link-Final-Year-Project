<template>
    <div class="mx-2">
        <div><span style="font-size: 14px;font-weight: 600">Beneficiary Verified Payments </span> </div>
        <div>
            <a class="link-pointer" @click.prevent="downloadPDF" style="font-size: 30px"><img
                    src="/images/icons/icon.pdf.png" style="width:55px;height: 55px;" alt=""></a>
        </div>

    </div>
</template>

<script>

import { PDFDocument, StandardFonts, rgb } from "pdf-lib";
import moment from 'moment';
import Swal from 'sweetalert2';
import {usePage} from "@inertiajs/vue3"
export default {
    name: 'beneficiary-payments',

    props: {
        createList:Boolean,
        group: {
            type: Object,
            default: null

        },
        participants: {
            type: Array,
            default: [],

        }
    },
    data() {
        return { pdfFile: null, PDFDocument, StandardFonts, rgb };
    },
    setup: (props, ctx) => {


        return {}
    },
    watch:{
        createList(newValue){
            if(newValue){
                this.generatePdf();
            }
        }
    },
    methods: {
        addNewPage(pdfDoc) {
            const newPage = pdfDoc.addPage();
            const { height } = newPage.getSize();
            const tableTopY = height - margin;
            drawRow(newPage, tableTopY, headers);
            return newPage;
        },
        async createTable(entries) {
            let self = this;

            const pdfDoc = await self.PDFDocument.create();
            const unicefLogoUrl = "/images/unicef.logo.blue.png"
            const logoBytes = await fetch(unicefLogoUrl).then(res=>res.arrayBuffer());
            const unicefLogo = await pdfDoc.embedPng(logoBytes);

            // Set up some basic variables
            const titleFontSize = 10;
            const headerFooterFontSize = 8;
            const fontSize = 9;
            const timesRomanFont = await pdfDoc.embedFont(self.StandardFonts.TimesRoman);
            const margin = 50;
            const rowHeight = 20;
            const colWidths = [40,140, 92, 32, 85, 82, 92, 86, 72]; // Example column widths
            const headers = ['SN','UNIQ ID', 'NAME', 'GDR', 'FACILITY', 'PHONE', 'AMOUNT TO PAY', 'AMOUNT PAID', 'PAYMENT DATE']; //
            const headerColor = rgb(0.75, 0.75, 0.75); // Light grey
            const stripeColor = rgb(0.95, 0.95, 0.95); // Very light grey
            const titleText = 'My Table Title JDSJKF KJDSJKFS djskfjkse fjiioeiofdsjk';
            const headerText = 'Page Header';
            const footerText = 'Page Footer';
            // Function to split text into lines based on column width
            const splitTextIntoLines = (text, maxWidth) => {
                const words = text.split(' ');
                const lines = [];
                let currentLine = words[0];

                for (let i = 1; i < words.length; i++) {
                    const word = words[i];
                    const width = timesRomanFont.widthOfTextAtSize(currentLine + ' ' + word, fontSize);
                    if (width < maxWidth) {
                        currentLine += ' ' + word;
                    } else {
                        lines.push(currentLine);
                        currentLine = word;
                    }
                }
                lines.push(currentLine);
                return lines;
            };
            // Function to draw a table row
            const drawRow = (page, y, row, fillColor = null) => {
                let x = margin;
                let maxRowHeight = rowHeight;
                const cellLines = row.map((cell, i) => splitTextIntoLines(cell, colWidths[i]));

                // Determine the maximum row height based on the number of lines in each cell
                cellLines.forEach(lines => {
                    const cellHeight = lines.length * rowHeight;
                    if (cellHeight > maxRowHeight) {
                        maxRowHeight = cellHeight;
                    }
                });
                for (let i = 0; i < row.length; i++) {
                    if (fillColor) {
                        page.drawRectangle({
                            x,
                            y: y - maxRowHeight,
                            width: colWidths[i],
                            height: maxRowHeight,
                            color: fillColor,
                        });
                    }


                    const lines = cellLines[i];
                    lines.forEach((line, j) => {
                        page.drawText(line, {
                            x: x + 5,
                            y: y - (j + 1) * rowHeight + 5,
                            size: fontSize,
                            font: timesRomanFont,
                            color: self.rgb(0, 0, 0),
                        });
                    });
                    x += colWidths[i];
                }

                // Draw row border
                page.drawLine({
                    start: { x: margin, y: y - maxRowHeight },
                    end: { x: margin + colWidths.reduce((a, b) => a + b, 0), y: y - maxRowHeight },
                    thickness: 1,
                    color: self.rgb(0.811, 0.84, 0.86),
                });
                return maxRowHeight;
            };



            // Function to draw column borders
            const drawColumnBorders = (page, y, height) => {
                let x = margin;
                for (let i = 0; i <= colWidths.length; i++) {
                    page.drawLine({
                        start: { x, y },
                        end: { x, y: y - height },
                        thickness: 1,
                        color: self.rgb(0.811, 0.84, 0.86),
                    });
                    if (i < colWidths.length) {
                        x += colWidths[i];
                    }
                }
            };

            // Function to add a new page and draw headers
            function addNewPage(pdfDoc,pageNumber) {
                const newPage = pdfDoc.addPage([842, 595]); // Landscape orientation: width 842, height 595 (A4 size)
                const { height,width } = newPage.getSize();

                const titleY = height - margin / 2;

                // Draw the header
                newPage.drawText("Beneficiary Verified List", {
                    x: margin,
                    y: height - margin / 3,
                    size: headerFooterFontSize,
                    lineHeight: 1.0,
                    font: timesRomanFont,
                    color: rgb(0, 0, 0),
                });

                // Draw the title
                newPage.drawText("Service Provider: "+self.$props.group?.provider.name, {
                    x: margin,
                    y: titleY - 5,
                    size: titleFontSize,
                    font: timesRomanFont,
                    lineHeight: 1.0,
                    color: rgb(0, 0, 0),
                });
                 // Draw the title
                 newPage.drawText("FSP: "+usePage().props.auth.user?.name, {
                    x: margin,
                    y: titleY - 20,
                    size: titleFontSize,
                    font: timesRomanFont,
                    lineHeight: 1.0,
                    color: rgb(0, 0, 0),
                });
                // Draw the logo
               const pngDims = unicefLogo.scale(0.5);
                newPage.drawImage(unicefLogo, {
                    x:width - (margin*3.25),
                    y: titleY - 20,
                    width:pngDims.width,
                    height:pngDims.height,
                });

                // Draw the footer
                newPage.drawText("UNICEF DOP-SSD "+(moment().format("Y-MMM-D h:m a")), {
                    x: margin,
                    y: margin / 3,
                    size: headerFooterFontSize,
                    font: timesRomanFont,
                    lineHeight: 1.0,
                    color: rgb(0, 0, 0),
                });
                // Draw the page number
                newPage.drawText(`Page ${pageNumber}`, {
                    x: width - margin - 50,
                    y: margin / 4,
                    size: headerFooterFontSize,
                    font: timesRomanFont,
                    lineHeight: 1.0,
                    color: rgb(0, 0, 0),
                });

                const tableTopY = (titleY - titleFontSize - rowHeight) - 15;
                drawRow(newPage, tableTopY, headers, headerColor);
                // drawColumnBorders(newPage, tableTopY, height - 2 * margin);
                return newPage;
            }

            let pageNumber = 1;
            let page = addNewPage(pdfDoc,pageNumber);
            // Draw table rows dynamically with page breaks
            let { height } = page.getSize();
            let y = (height - margin - rowHeight) -18;
            for (let i = 0; i < entries.length; i++) {
                const entry = entries[i];
                if (y < (margin)) {
                    pageNumber++;
                    page = addNewPage(pdfDoc,pageNumber);
                    ({ height } = page.getSize());
                    y = (height - margin - rowHeight)-18;
                }
                const fillColor = i % 2 === 0 ? stripeColor : null;

                const drawnRowHeight = drawRow(page, y, entries[i], fillColor);
                y -= drawnRowHeight;

            }

            const pdfBytes = await pdfDoc.save();
            const blob = new Blob([pdfBytes], { type: "application/pdf" });
            const pdfFile = new File([blob], "beneficiary list.pdf", { type: "application/pdf", lastModified: new Date() });
            self.$emit('generatePDFEvent',pdfFile);

            return pdfBytes;

        },
        async downloadPDF(){
            Swal.fire({
                title: 'Please wait...',
                didOpen:()=>{
                    Swal.showLoading();
                }
            });
            let self = this;
            let entries = [];
            for (let index = 0; index < self.$props.participants.length; index++) {
                const entry = self.$props.participants[index];
                entries.push([`${index + 1}`,`${entry.uniq_id}`, `${entry.firstname || ''} ${entry.surname || ''} ${entry.othername || ''}`, `${entry.gender || ''}`, `${entry.health_facilityboma || ''}`, `${entry.phone_number || ''}`, `${entry.amount_to_pay || ''}`, `${entry.amount_paid || ''}`, `${entry.payment_date || ''}`]);
            }
            const fileItem = await self.createTable(entries)
            download(fileItem, "beneficiary list.pdf", "application/pdf");
            Swal.close();
        },
        async generatePdf() {
            let self = this;
            Swal.fire({
                title: 'Please wait...',
                didOpen:()=>{
                    Swal.showLoading();
                }
            });

            let entries = [];
            for (let index = 0; index < self.$props.participants.length; index++) {
                const entry = self.$props.participants[index];
                entries.push([`${index + 1}`,`${entry.uniq_id}`, `${entry.firstname} ${entry.surname} ${entry.othername}`, `${entry.gender}`, `${entry.health_facilityboma}`, `${entry.phone_number}`, `${entry.amount_to_pay}`, `${entry.amount_paid}`, `${entry.payment_date}`]);
            }

            await self.createTable(entries);
            Swal.close();
        },
        async beforeDownload({ html2pdf, options, pdfContent }) {

            let fileData = await html2pdf().set(options).from(pdfContent).toPdf().get('pdf').then((pdf) => {
                const totalPages = pdf.internal.getNumberOfPages()
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i)
                    pdf.setFontSize(10)
                    pdf.setTextColor(150)
                    pdf.text('Page ' + i + ' of ' + totalPages, (pdf.internal.pageSize.getWidth() * 0.88), (pdf.internal.pageSize.getHeight() - 0.3))
                }
            }).save();
            console.log({ fileData })
        },
        async hasDownloaded(fileItem) {
            console.log("hasGenerated", { fileItem });
        },
        async onProgress(event) {
            console.log("onProgress", event);
        }

    },

    mounted() {
        console.log('mounted');


    }
}
</script>

<style lang="scss" scoped></style>
