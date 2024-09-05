<?php
session_start();
?>
    <!DOCTYPE html>
    <html>
    <head><?php include_once "src/partials/head.php"; ?></head>
    <body>
    <?php
    include "config.php";  
    include "lib.php";	
    include 'src/auth/login.php';

    if (!isset($_SESSION['Login_Prihlasovacie_meno']))  // nie je prihlaseny
    {
        exit;
    }
    include_once "src/partials/navbar.php"; // navigacia

    if(!strpos($_SERVER['HTTP_REFERER'], 'src/form/nahradny_diel.php')){
        $_SESSION["hlaska"] = "";
    }

    if(ZistiPrava("zobrazNahradneDiely",$dblink) == 0){
        echo "<span class='oznam cervene'>Nemáte práva na zobrazenie náhradných dielov.</span>";
        exit;
    }

    ?>
    <?php /*if($_SESSION["hlaska"])	echo $_SESSION["hlaska"];// upozornenie */?>
    <div id='myapp'>
        <div class="container-fluid col-md-12 col-12">
            <div class="graybox row border0 d-flex align-items-end justify-content-center">
                <?php if(ZistiPrava("editNahradneDiely",$dblink) == 0): ?>
                    <div class="col-md-4 col-12">
                        <h2>Náhradné diely</h2>
                    </div>
                <?php else: ?>
                    <div class="col-md-3 col-12">
                        <h2>Náhradné diely</h2>
                    </div>
                    <div class="col-md-3 col-12 filtre">
                        <input type='button' class="btn padding" @click='newRow' value='Nový náhradný diel'>
                    </div>
                <?php endif; ?>

                <div class="marginbottom col-md-3 col-12 flex-column d-flex align-items-center justify-content-center roboto-light filtre">
                    Kategória:
                    <br>
                    <select id=kategoria"  v-model="kategoria" class="form-select" class="select_height" @change="recordByCategory">
                        <option value="" selected>Všetky</option>
                        <template v-for="category in categories">
                            <option v-bind:value="category.Kat_nazov" >{{ category.Kat_nazov }}</option>
                        </template>
                    </select>
                </div>

                <div class="col-md-3 col-12 d-flex align-items-end justify-content-center">
                    <div class="input-group flex-nowrap filtre hladaj">
                        <input type="text" class="form-control" placeholder="Hladať" id="search" v-model="search" v-on:keyup.esc="clearSearch" v-on:keyup.enter="recordBySearch">
                        <a @click='clearSearch'><i class="fa fa-times cursor zmaz"></i></a>
                        <button class="input-group-text" id="addon-wrapping" @click="recordBySearch" ><i class="fa fa-search lupa cursor" ></i></button>
                    </div>
                </div>

            </div>
        </div>

        <div class="mytable">
            <div class="container-fluid col-12 col-md-12 roboto-light">
                <div class="row tr border0" >
                    <div class="col-md-2 col-6 th">Evidencne cislo</div>
                    <div class="col-md-3 col-6 th">Názov</div>
                    <div class="col-md-2 col-6 th">Kategória</div>
                    <div class="col-md-1 col-6 th">Mnozstvo</div>
                    <div class="col-md-1 col-6 th">Umiestnenie</div>
                    <div class="col-md-3 col-6 th"></div>
                </div>
                <div class="row tr" v-for="nahradny_diel in paginatedEmployees">
                    <div class="col-md-2 col-6 td ">
                        {{ nahradny_diel.Diel_evidencne_cislo }}
                    </div>
                    <div class="col-md-3 col-6 td " >
                        {{ nahradny_diel.Diel_nazov }}
                    </div>
                    <div class="col-md-2 col-6 td " >
                        {{ nahradny_diel.Kat_nazov }}
                    </div>
                    <div class="col-md-1 col-6 td border0">
                        {{ nahradny_diel.Diel_mnozstvo }} {{ nahradny_diel.Diel_jednotka }}
                    </div>
                    <div class="col-md-1 col-6 td border0">
                        {{ nahradny_diel.Diel_umiestnenie }}
                    </div>
                    <div class="col-md-3 col-6 td border0">
                        <?php
                        if(ZistiPrava("editNahradneDiely",$dblink) == 0) {
                            ikony_zobraz_nahradne_diely();
                        }
                        else{
                            ikony_nahradne_diely();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

             <!-- Prvky ovládania stránok -->
        <div class="graybox2">
            <br>
            <div v-if="totalPages > 1" class="d-flex align-items-center justify-content-between float-start">
                <button
                        :class="['btn', { 'btn': !(currentPage === 1) }, { 'btn': currentPage === 1 }]"
                        @click="previousPage"
                        :disabled="currentPage === 1">
                    <i class="fa fa-chevron-left cursor ikona"></i>
                </button>
                <div class="pagination" v-for="page in Array.from({ length: totalPages }, (_, i) => i + 1)">
                    <button
                            :class="['btn', { 'btn-primary': currentPage === page }]"
                            @click="goToPage(page)" :disabled="currentPage === page" class="ikona">{{ page }}

                    </button>
                </div>
                <button
                        :class="['btn', { 'btn': !(currentPage === totalPages) }, { 'btn': currentPage === totalPages }]"
                        @click="nextPage"
                        :disabled="currentPage === totalPages">
                    <i class="fa fa-chevron-right cursor ikona"></i>
                </button>
            </div>
            <div class="float-end roboto-light">
                Počet záznamov na stranu:
                <select class="form-select-sm" id="itemsPerPage" v-model="itemsPerPage" @change="updateView">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="8">8</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>
        </div>
        <br><br><br><br><br>
<!--        <div class='oznam'>{{oznam.Zam_meno}} {{oznam.Zam_priezvisko }} bol vymazaný z databázy </div>-->

        <transition name="notification">
            <div v-if="notificationVisible" class="notification show">
                <span class="closebtn" @click="notificationVisible = false">&times;</span>
                <div class="text">
                    <i class="info-icon fas fa-info-circle"></i>
                    <?php
                    if(isset($_SESSION["hlaska"])){
                        echo $_SESSION["hlaska"];
                    }
                    ?>
                </div>
                <div class="timer" v-if="notificationVisible"></div>
            </div>
        </transition>

        <transition name="notification2">
            <div v-if="notificationDelVisible" class="notification show">
                <span class="closebtn" @click="notificationDelVisible = false">&times;</span>
                <div class="text">
                    <i class="info-icon fas fa-info-circle"></i>
                    {{oznam.nazov}} bol vymazaný z databázy
                </div>
                <div class="timer" v-if="notificationDelVisible"></div>
            </div>
        </transition>

        <transition name="notification3">
            <div v-if="notificationChybaVisible" class="notification_chyba show">
                <span class="closebtn" @click="notificationChybaVisible = false">&times;</span>
                <div class="text">
                    <i class="info-icon fas fa-circle-exclamation"></i>
                    {{ oznam.pouziva_sa }}
                </div>
                <div class="timer_chyba" v-if="notificationChybaVisible"></div>
            </div>
        </transition>



        <footer>
            <?php include_once "src/partials/footer.php"; ?>
        </footer>
    </div>



    <!-- Script -->
    <script>
        var app = new Vue({
            el: '#myapp',
            data: {
                nahradny_diel: "",
                Nahradny_dielID: 0,
                Diel_nazov: "",
                search: "",
                selectlist: "",
                categories: "",
                kategoria: "",
                oznam: "",
                hlaska: '',
                notificationVisible: false,
                notificationDelVisible: false,
                notificationChybaVisible: false,
                currentPage: 1,
                itemsPerPage: 8,
            },
            mounted: function(){
                this.allRecords();
                this.listCategory();
                var hlaska = "<?php echo isset($_SESSION['hlaska']) ? $_SESSION['hlaska'] : ""; ?>";
                if(hlaska !== "") {
                    // Display the notification
                    this.notificationVisible = true;
                    // Hide the notification after 5 seconds
                    setTimeout(() => this.notificationVisible = false, 5000);
                }
            },

            computed:{
                // Počítač pre stránkované dáta
                paginatedEmployees: function() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.nahradny_diel.slice(start, end);
                },
                totalPages: function() {
                    return Math.ceil(this.nahradny_diel.length / this.itemsPerPage);
                },
                /*paginatedEmployees: function() {
                    return this.getPageData();
                }*/
            },

            methods: {
                allRecords: function(){

                    axios.get('src/read/read_nahradne_diely.php')
                        .then(function (response) {
                            app.nahradny_diel = response.data;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },

                searchInput() {
                    if (this.search === '') {
                        this.clearSearch();
                    }},

                recordBySearch: function(){
                    if(app.search !=="" && this.kategoria === ""){
                        axios.get('src/read/read_nahradne_diely.php', {
                            params: {
                                search: app.search
                            }
                        })
                            .then(function (response) {
                                app.nahradny_diel = response.data;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                    if(app.search !=="" && this.kategoria !== ""){
                        axios.get('src/read/read_nahradne_diely.php', {
                            params: {
                                search: app.search,
                                kategoria: this.kategoria
                            }
                        })
                            .then(function (response) {
                                app.nahradny_diel = response.data;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                },

                recordByCategory: function(){
                    if(this.kategoria === "Všetky" || this.kategoria === ""){
                        if(app.search !==""){
                            this.recordBySearch();
                        }
                        else{
                           this.allRecords();
                        }

                    } else {
                        if(app.search !==""){ // Ak je vyplnena search tak nech aj vyhlada aj vyfiltruje
                            axios.get('src/read/read_nahradne_diely.php', {
                                params: {
                                    kategoria: this.kategoria,
                                    search: app.search
                                }
                            })
                                .then(function (response) {
                                    app.nahradny_diel = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }
                        else{
                            axios.get('src/read/read_nahradne_diely.php', {
                                params: {
                                    kategoria: this.kategoria,
                                }
                            })
                                .then(function (response) {
                                    app.nahradny_diel = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }

                    }
                },
                listCategory: function(){
                    axios.get('src/read/read_nahradne_diely.php', {
                        params: {
                            list: "Kat_nazov"
                        }
                    })
                        .then(function (response) {
                            app.categories = response.data;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                },

                clearSearch: function(){
                    app.search='';
                    if(this.kategoria !== "Všetky"){
                        this.recordByCategory();
                    }
                    else{
                        this.allRecords();
                    }
                },

                deleteRow: function(id){
                    var nahradny_diel = this.nahradny_diel.find(emp => emp.Nahradny_dielID === id);
                    if (nahradny_diel) {
                        var c = confirm("Ste si istý, že chcete zmazať náhradný diel "+ nahradny_diel.Diel_nazov +"?" );
                        if (c) {
                            axios.get('src/zmazat/zmazat_nahradny_diel.php', {
                                params: {
                                    Nahradny_dielID: nahradny_diel.Nahradny_dielID,
                                    Diel_nazov: nahradny_diel.Diel_nazov
                                }
                            })
                                .then(function (response) {
                                    if (response.data.status === "success") {
                                        app.oznam = response.data;
                                        app.allRecords();
                                        app.notificationChybaVisible = false;
                                        app.notificationDelVisible = true;
                                        setTimeout(() => app.notificationDelVisible = false, 5000);

                                    } else if (response.data.status === "error") {
                                        app.oznam = response.data;
                                        app.allRecords();
                                        app.notificationDelVisible = false;
                                        app.notificationChybaVisible = true;
                                        setTimeout(() => app.notificationChybaVisible = false, 10000);
                                    }

                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }
                    }
                    return ;
                },

                editRow: function(Nahradny_dielID){
                    location.replace("src/form/nahradny_diel.php?Nahradny_dielID="+Nahradny_dielID);

                },

                viewRow: function(Nahradny_dielID){
                    location.replace("src/form/nahradny_diel.php?Nahradny_dielID="+Nahradny_dielID+"&zobrazit=1");
                },

                newRow: function(){
                    location.replace("src/form/nahradny_diel.php");
                },

                nextPage: function() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage += 1;
                    }
                },
                previousPage: function() {
                    if (this.currentPage > 1) {
                        this.currentPage -= 1;
                    }
                },
                updateView() {
                    // Convert string value to number
                    this.itemsPerPage = Number(this.itemsPerPage);

                    // Reset currentPage to show first set of results
                    this.currentPage = 1;

                    // Trigger the function that re-fetches records
                    if(app.search !==""){
                        this.recordBySearch();
                    }

                    if(app.kategoria !==""){
                        this.recordByCategory();
                    }

                    if(app.kategoria !=="" && app.search !==""){
                        this.recordByCategory();
                    }


                },

                goToPage: function(page) {
                    this.currentPage = page;
                }

            }

        })

    </script>

    <!-- jquery -->
    <script src="mdbootstrap/js/jquery-3.7.1.js"></script>

    </body>
    </html>

<?php
    function ikony_nahradne_diely(){

?>

    <div class="container-fluid ikony d-flex col-md-12 col-12">
        <div class="row border0 justify-content-center ">

            <div class="col-md-4 col-4 cursor">
                <button class="btn ikona padding" title="Zobraziť" @click='viewRow(nahradny_diel.Nahradny_dielID)'><i class="fa-regular fa-eye"></i></button>
            </div>

            <div class="col-md-4 col-4 cursor">
                <button class="btn ikona padding" title="Upraviť" @click='editRow(nahradny_diel.Nahradny_dielID)'><i class="fa-regular fa-pen-to-square"></i></button>
            </div>

            <div class="col-md-4 col-4 cursor" id="zmazat_riadok">
                <button class="btn ikona padding" title="Vymazať" @click='deleteRow(nahradny_diel.Nahradny_dielID)'><i class="fa-regular fa-trash-can"></i></button>
            </div>

        </div>

    </div>
    <?php
    }
    ?>

<?php
function ikony_zobraz_nahradne_diely(){

    ?>

    <div class="container-fluid ikony d-flex col-md-12 col-12">
        <div class="row border0 justify-content-center ">

            <div class="col-md-12 col-12 cursor">
                <button class="btn ikona padding" title="Zobraziť" @click='viewRow(nahradny_diel.Nahradny_dielID)'><i class="fa-regular fa-eye"></i></button>
            </div>

        </div>

    </div>
    <?php
}
?>

