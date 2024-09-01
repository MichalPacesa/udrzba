<?php
session_start();
?>
    <!DOCTYPE html>
    <html>
    <head><?php include "head.php" ?></head>

    <body>
    <?php
    include_once "config.php";  //
    include_once "lib.php";	//	funkcie
    include_once 'login.php';
    if (!isset($_SESSION['Login_Prihlasovacie_meno']))  // nie je prihlaseny
    {
        exit;
    }
    include_once "src/partials/navbar.php"; // navigacia
    if(!strpos($_SERVER['HTTP_REFERER'], 'zamestnanec.php')){
        $_SESSION["hlaska"] = "";
    }

    if(ZistiPrava("zamestnanci",$dblink) == 0){
        echo "<span class='cervene'>Nemáte práva na zobrazenie zamestnancov.</span>";
        exit;
    }

    ?>
    <?php /*if($_SESSION["hlaska"])	echo $_SESSION["hlaska"];// upozornenie */?>
    <div id='myapp'>
        <div class="container-fluid col-md-12 col-12">
            <div class="graybox row border0 d-flex align-items-end justify-content-center">
                <div class="col-md-3 col-12">
                    <h2>Zamestnanci</h2>
                </div>

                <div class="col-md-3 col-12 filtre">
                    <input type='button' class="btn padding" @click='newRow' value='Nový zamestnanec'>
                </div>

                <div class="marginbottom col-md-3 col-12 flex-column d-flex align-items-center justify-content-center roboto-light filtre">
                    Pracovná pozícia:
                    <br>
                    <select id=pozicia  v-model="pozicia" class="form-select" class="select_height " @change="recordByPosition" >
                        <option value="" selected>Všetky</option>
                        <template v-for="position in positions">
                            <option v-bind:value="position.Zam_pozicia" >{{position.Zam_pozicia}}</option>
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
                    <div class="col-md-3 col-6 th">Meno a priezvisko</div>
                    <div class="col-md-3 col-6 th">Pracovná pozícia</div>
                    <div class="col-md-3 col-6 th">Telefon</div>
                    <div class="col-md-3 col-6 th"></div>
                </div>
                <div class="row tr" v-for="zamestnanec in paginatedEmployees">
                    <div class="col-md-3 col-6 td ">
                        {{ zamestnanec.Zam_meno }}&nbsp;{{ zamestnanec.Zam_priezvisko }}
                    </div>
                    <div class="col-md-3 col-6 td " >
                        {{ zamestnanec.Zam_pozicia }}
                    </div>
                    <div class="col-md-3 col-6 td border0">
                        {{ zamestnanec.Zam_telefon }}
                    </div>
                    <div class="col-md-3 col-6 td border0">
                        <?php //include "ikony_zamestnanec.php"
                        ikony_zamestnanec();
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
                    {{ oznam.menopriezvisko }} bol vymazaný z databázy
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
            <?php include_once "footer.php"; ?>
        </footer>
    </div>



    <!-- Script -->
    <script>
        var app = new Vue({
            el: '#myapp',
            data: {
                zamestnanec: "",
                ZamestnanecID: 0,
                Zam_meno: "",
                Zam_priezvisko: "",
                Zam_pozicia: "",
                Zam_telefon: "",
                search: "",
                selectlist: "",
                positions: "",
                pozicia: "",
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
                this.listPosition();
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
                    return this.zamestnanec.slice(start, end);
                },
                totalPages: function() {
                    return Math.ceil(this.zamestnanec.length / this.itemsPerPage);
                },
                /*paginatedEmployees: function() {
                    return this.getPageData();
                }*/
            },

            methods: {
                allRecords: function(){

                    axios.get('read_zamestnanci.php')
                        .then(function (response) {
                            app.zamestnanec = response.data;
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
                    if(app.search !==""){
                        axios.get('read_zamestnanci.php', {
                            params: {
                                search: app.search
                            }
                        })
                            .then(function (response) {
                                app.zamestnanec = response.data;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                },
                recordByPosition: function(){
                    if(this.pozicia === "Všetky" || this.pozicia === ""){
                        if(app.search !==""){
                            this.recordBySearch();
                        }
                        else{
                           this.allRecords();
                        }

                    } else {
                        if(app.search !==""){ // Ak je vyplnena search tak nech aj vyhlada aj vyfiltruje
                            axios.get('read_zamestnanci.php', {
                                params: {
                                    pozicia: this.pozicia,
                                    search: app.search
                                }
                            })
                                .then(function (response) {
                                    app.zamestnanec = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }
                        else{
                            axios.get('read_zamestnanci.php', {
                                params: {
                                    pozicia: this.pozicia,
                                }
                            })
                                .then(function (response) {
                                    app.zamestnanec = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }

                    }
                },
                listPosition: function(){
                    axios.get('read_zamestnanci.php', {
                        params: {
                            list: "Zam_pozicia"
                        }
                    })
                        .then(function (response) {
                            app.positions = response.data;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                },

                clearSearch: function(){
                    app.search='';
                    if(this.pozicia !== "Všetky"){
                        this.recordByPosition();
                    }
                    else{
                        this.allRecords();
                    }
                },

                deleteRow: function(id){
                    var zamestnanec = this.zamestnanec.find(emp => emp.ZamestnanecID === id);
                    if (zamestnanec) {
                        var c = confirm("Ste si istý, že chcete zmazať zamestnanca "+ zamestnanec.Zam_meno+" "+ zamestnanec.Zam_priezvisko +"?" );
                        if (c) {
                            axios.get('zmazat_zamestnanca.php', {
                                params: {
                                    ZamestnanecID: zamestnanec.ZamestnanecID,
                                    PouzivatelID: zamestnanec.PouzivatelID,
                                    Zam_meno: zamestnanec.Zam_meno,
                                    Zam_priezvisko: zamestnanec.Zam_priezvisko
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

                editRow: function(ZamestnanecID){
                    location.replace("zamestnanec.php?ZamestnanecID="+ZamestnanecID);

                },

                viewRow: function(ZamestnanecID){
                    location.replace("zamestnanec.php?ZamestnanecID="+ZamestnanecID+"&zobrazit=1");
                },

                newRow: function(){
                    location.replace("zamestnanec.php");
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

                    if(app.pozicia !==""){
                        this.recordByPosition();
                    }

                    if(app.pozicia !=="" && app.search !==""){
                        this.recordByPosition();
                    }


                },
                /*getPageData() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.zamestnanec.slice(start, end);
                },*/

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

<?php function ikony_zamestnanec(){ ?>

    <div class="container-fluid ikony d-flex col-md-12 col-12">
        <div class="row border0 justify-content-center ">
            <div class="col-md-4 col-4 cursor">
                <button class="btn ikona padding" title="Zobraziť" @click='viewRow(zamestnanec.ZamestnanecID)'><i class="fa-regular fa-eye"></i></button>
            </div>

            <div class="col-md-4 col-4 cursor">
                <button class="btn ikona padding" title="Upraviť" @click='editRow(zamestnanec.ZamestnanecID)'><i class="fa-regular fa-pen-to-square"></i></button>
            </div>

            <div class="col-md-4 col-4 cursor" id="zmazat_riadok">
                <button class="btn ikona padding" title="Vymazať" @click='deleteRow(zamestnanec.ZamestnanecID)'><i class="fa-regular fa-trash-can"></i></button>
            </div>
        </div>

    </div>
    <?php
}
?>