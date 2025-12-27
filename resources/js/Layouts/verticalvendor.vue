<script>
import NavBar from "../Components/nav-bar-vendor.vue";
import SideBar from "../Components/side-bar.vue";
import RightBar from "../Components/right-bar.vue";
import Footer from "../Components/footer.vue";

export default {
    name: 'Vertical',
    components: { NavBar, SideBar, RightBar, Footer },
    data() {
        return {
            isMenuCondensed: false
        };
    },
    mounted(){
        // document.body.classList.remove('side-bg');
    },
    beforeCreate() {

        if (localStorage.getItem("layout")) {
            var layout = localStorage.getItem("layout");
            console.log({layout});
            layout = JSON.parse(layout);

            console.log({layout});


            // layout boxed
            if (layout.width == "boxed") {
                document.body.setAttribute('data-layout-size', 'boxed');
            }

            // sidebar
            if (layout.sidebar) {
                this.$root.changeSidebar(layout.sidebar);
            }

            if (layout.mode == "dark") {
                // document.body.setAttribute('data-layout-mode', 'dark');
                document.body.setAttribute('data-layout-mode', 'light');
            }

        }

    },
    created: () => {
        document.body.removeAttribute("data-layout", "horizontal");
        document.body.removeAttribute("data-topbar", "dark");
        document.body.setAttribute("data-topbar", "light");

    },
    methods: {
        toggleMenu() {
            document.body.classList.toggle("sidebar-enable");

            if (window.screen.width >= 992) {
                // eslint-disable-next-line no-unused-vars
                document.body.classList.toggle("vertical-collpsed");
            } else {
                // eslint-disable-next-line no-unused-vars
                document.body.classList.remove("vertical-collpsed");
            }
            this.isMenuCondensed = !this.isMenuCondensed;
        },
        toggleRightSidebar() {
            document.body.classList.toggle("right-bar-enabled");
        },
        hideRightSidebar() {
            document.body.classList.remove("right-bar-enabled");
        }
    }
};
</script>

<template>
    <div>
        <div id="layout-wrapper">
            <NavBar />
            <SideBar :is-condensed="isMenuCondensed" />
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="main-content">
                <div class="page-content">
                    <!-- Start Content-->
                    <div class="container-fluid">
                        <slot />
                    </div>
                </div>
                <Footer />
            </div>
            <!-- <RightBar /> -->
        </div>
    </div>
</template>
