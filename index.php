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

    // Ak nie je pouzivatel prihlaseny tak exit
    if (!isset($_SESSION['Login_Prihlasovacie_meno'])){  
        exit;
    }
    include_once "src/partials/navbar.php"; // navigacia

    if (isset($_SERVER['HTTP_REFERER']) &&
        (!strpos($_SERVER['HTTP_REFERER'], 'src/form/porucha.php') &&
            !strpos($_SERVER['HTTP_REFERER'], 'src/zmena/zmena_hesla.php') &&
            !strpos($_SERVER['HTTP_REFERER'], 'src/form/oprava.php'))){
        $_SESSION["hlaska"] = "";
    }

    if(ZistiPrava("Zobraz_poruchy",$dblink) == 0){
        echo "<span class='oznam'>Nemáte práva na zobrazenie porúch.</span>";
        exit;
    }

    ?>

    <div id='myapp'>
        <div class="graybox container-fluid col-md-12 col-12">
            <div class="row border0 d-flex align-items-end justify-content-center">
                <div class="col-md-3 col-12 justify-content-center">
                    <h2>Poruchy</h2>
                </div>

                <div class="col-md-3 col-12 filtre">
                    <input type='button' class="btn padding" @click='newRow' value='Nová porucha'>
                </div>

                <div class="marginbottom col-md-3 col-12 flex-column d-flex align-items-center justify-content-center roboto-light filtre">
                    Stroj:
                    <br>
                    <select v-model="stroj" id="stroj" class="form-select select_height" @change="recordByMachine(stroj)">
                        <option value="0" selected>Všetky</option>
                        <template v-for="machine in machines">
                            <option v-bind:value="machine.StrojID" >{{machine.Stroj_nazov}}</option>
                        </template>
                    </select>
                </div>

                <div class="col-md-3 col-12 d-flex align-items-end justify-content-center">
                    <div class="input-group flex-nowrap filtre hladaj">
                        <input type="text" class="form-control" placeholder="Hladať" v-model="search" id="search"  v-on:keyup.esc="clearSearch" v-on:keyup.enter="recordBySearch">
                        <a @click='clearSearch'><i class="fa fa-times cursor zmaz"></i></a>
                        <button class="input-group-text" id="addon-wrapping" @click="recordBySearch" ><i class="fa fa-search lupa cursor" ></i></button>
                    </div>
                </div>

            </div>

            <div class="row border0 d-flex align-items-end justify-content-center">

                <div class="col-md-3 col-12">
                    <!--Prazdny-->
                </div>

                <div class="col-md-3 col-12">
                    <!--Prazdny-->
                </div>

                <div class="marginbottom col-md-3 col-12 flex-column d-flex align-items-center justify-content-center roboto-light filtre">
                    Stav:
                    <br>
                    <select v-model="stav" id="stav" class="form-select" class="select_height " @change="recordByState(stav)" >
                        <option value="0" selected>Všetky</option>
                        <template v-for="state in states">
                            <option v-bind:value="state.Por_stav" >{{getStavText(state.Por_stav)}}</option>
                        </template>
                    </select>
                </div>

                <div class="col-md-3 col-12">
                    <!--Prazdny-->
                </div>

            </div>
        </div>

        <div class="mytable">
            <div class="container-fluid col-12 col-md-12 roboto-light">
                <div class="row tr border0" >
                    <div class="col-md-2 col-6 th">Názov poruchy</div>
                    <div class="col-md-1 col-6 th">Dátum vzniku</div>
                    <div class="col-md-2 col-6 th">Stroj</div>
                    <div class="col-md-2 col-6 th">Pridelený zamestnanec</div>
                    <div class="col-md-2 col-6 th">Stav</div>
                    <div class="col-md-3 col-6 th"></div>
                </div>
                <div class="row tr" v-for="porucha in paginatedPoruchy">
                    <div class="col-md-2 col-6 td ">
                        {{ porucha.Por_nazov }}
                    </div>
                    <div class="col-md-1 col-6 td ">
                        {{ formatDate(porucha.Por_datum_vzniku) }}
                    </div>
                    <div class="col-md-2 col-6 td" >
                        {{ porucha.Stroj_nazov }}
                    </div>
                    <div class="col-md-2 col-6 td border0">
                        {{ porucha.Zam_meno }} {{ porucha.Zam_priezvisko }}
                    </div>
                    <div class="col-md-2 col-6 td ">
                        {{ getStavText(porucha.Por_stav) }}
                    </div>
                    <div class="col-md-3 col-12 td border0">
                        <?php
                        ikony_porucha();
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
                <select class="form-select-sm" v-model="itemsPerPage" id="itemsPerPage" @change="updateView">
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

        <transition name="notification2">
            <div v-if="notificationDelVisible" class="notification show">
                <span class="closebtn" @click="notificationDelVisible = false">&times;</span>
                <div class="text">
                    <i class="info-icon fas fa-info-circle"></i>
                    Porucha {{oznam.Por_nazov }} bola vymazaná z databázy
                </div>
                <div class="timer" v-if="notificationDelVisible"></div>
            </div>
        </transition>

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

        <footer>
            <?php include_once "src/partials/footer.php";?>
        </footer>
    </div>


    <!-- Script -->
    <script>
        var app = new Vue({
            el: '#myapp',
            data: {
                porucha: "",
                ZamestnanecID: 0,
                Zam_meno: "",
                Zam_priezvisko: "",
                Zam_pozicia: "",
                Zam_telefon: "",
                search: "",
                selectlist: "",
                machines: "",
                stroj: "0",
                states: "",
                stav: "0",
                oznam: "",
                hlaska: '',
                notificationVisible: false,
                notificationDelVisible: false,
                currentPage: 1,
                itemsPerPage: 4,
                Por_stav: null,
                StavPoruchy: null,
                Por_stav_text: "",
                stav_old: null,
            },
            mounted: function(){
                this.allRecords();
                this.listMachine();
                this.listState();
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
                paginatedPoruchy: function() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.porucha.slice(start, end);
                },
                totalPages: function() {
                    return Math.ceil(this.porucha.length / this.itemsPerPage);
                }
            },

            methods: {
                allRecords: function(){

                    axios.get('src/read/read_poruchy.php')
                        .then(function (response) {
                            app.porucha = response.data;

                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },

                formatDate: function(value) {
                    let date = new Date(value);
                    let day = ("0" + date.getDate()).slice(-2);
                    let month = ("0" + (date.getMonth() + 1)).slice(-2);
                    let year = date.getFullYear();
                    let hours = ("0" + date.getHours()).slice(-2);
                    let minutes = ("0" + date.getMinutes()).slice(-2);
                    return `${day}.${month}.${year} ${hours}:${minutes}`;
                },

                searchInput() {
                    if (this.search === '') {
                        this.clearSearch();
                    }},

                recordBySearch: function(){
                    if(app.search !=="" && app.stav === "0" && app.stroj === "0"){
                        axios.get('src/read/read_poruchy.php', {
                            params: {
                                search: app.search
                            }
                        })
                            .then(function (response) {
                                app.porucha = response.data;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                    if(app.search !=="" && app.stav !== "0"){ // Ak je vyplnena search tak nech aj vyhlada aj vyfiltruje
                        axios.get('src/read/read_poruchy.php', {
                            params: {
                                Por_stav: app.stav,
                                search: app.search
                            }
                        })
                            .then(function (response) {
                                app.porucha = response.data;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                    if(app.search !=="" && app.stroj !== "0"){ // Ak je vyplnena search tak nech aj vyhlada aj vyfiltruje
                        axios.get('src/read/read_poruchy.php', {
                            params: {
                                StrojID: app.stroj,
                                search: app.search
                            }
                        })
                            .then(function (response) {
                                app.porucha = response.data;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                },
                recordByMachine: function(stroj_selected){
                    if(app.stroj === "0"){
                        if(app.search !==""){
                            this.recordBySearch();
                        }
                        else{
                            this.allRecords();
                        }

                    } else {
                        app.stav = "0";
                        if(app.search !==""){ // Ak je vyplnena search tak nech aj vyhlada aj vyfiltruje
                                axios.get('src/read/read_poruchy.php', {
                                    params: {
                                        StrojID: stroj_selected,
                                        search: app.search
                                    }
                                })
                                    .then(function (response) {
                                        app.porucha = response.data;
                                    })
                                    .catch(function (error) {
                                    console.log(error);
                                });
                        }
                        else{

                            axios.get('src/read/read_poruchy.php', {
                                params: {
                                    StrojID: stroj_selected
                                }
                            })
                                .then(function (response) {
                                    app.porucha = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }

                    }
                },
                recordByState: function(stav_selected){
                    if(app.stav === "0"){

                        if(app.search !==""){
                            this.recordBySearch();
                        }
                        else{
                            this.allRecords();
                        }

                    } else {
                        app.stroj = "0";
                        if(app.search !==""){ // Ak je vyplnena search tak nech aj vyhlada aj vyfiltruje
                            axios.get('src/read/read_poruchy.php', {
                                params: {
                                    Por_stav: stav_selected,
                                    search: app.search
                                }
                            })
                                .then(function (response) {
                                    app.porucha = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }
                        else{
                                axios.get('src/read/read_poruchy.php', {
                                params: {
                                    Por_stav: stav_selected,
                                }
                            })
                                .then(function (response) {
                                    app.porucha = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }

                    }
                },

                listMachine: function(){
                    axios.get('src/read/read_stroje.php', {
                        params: {
                            list: "Stroj_nazov",
                            porucha: "1",
                        }
                    })
                        .then(function (response) {
                            app.machines = response.data;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                },

                listState: function(){
                    axios.get('src/read/read_poruchy.php', {
                        params: {
                            list: "Por_stav"
                        }
                    })
                        .then(function (response) {
                            app.states = response.data;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                },

                clearSearch: function(){
                    app.search='';
                    if(app.stroj !== "0"){

                        this.recordByMachine(app.stroj);
                    }
                    else if(app.stav !== "0"){
                        this.recordByState(app.stav);
                    }
                    else{
                        this.allRecords();
                    }
                },

                deleteRow: function(id){
                    var porucha = this.porucha.find(emp => emp.PoruchaID === id);
                    if (porucha) {
                        var c = confirm("Ste si istý, že chcete zmazať poruchu "+ porucha.Por_nazov +"?" );
                        if (c) {
                            axios.get('src/zmazat/zmazat_poruchu.php', {
                                params: {
                                    PoruchaID: porucha.PoruchaID,
                                    Por_nazov: porucha.Por_nazov
                                }
                            })
                                .then(function (response) {
                                    app.oznam = response.data;
                                    // console.log(response.data);
                                    app.allRecords();
                                    app.notificationDelVisible = true;
                                    setTimeout(() => app.notificationDelVisible = false, 5000);

                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }
                    }
                    return ;
                },

                editRow: function(PoruchaID){
                    location.replace("src/form/porucha.php?PoruchaID="+PoruchaID);
                },

                viewRow: function(PoruchaID){
                    location.replace("src/form/porucha.php?PoruchaID="+PoruchaID+"&zobrazit=1");
                },

                newRow: function(){
                    location.replace("src/form/porucha.php");
                },

                repair: function(PoruchaID){
                    location.replace("src/form/oprava.php?PoruchaID="+PoruchaID);
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

                    if(app.stroj !=="0"){
                        this.recordByMachine(app.stroj);
                    }

                    if(app.stav !=="0"){
                        this.recordByState(app.stav);
                    }

                    if(app.stroj !=="0" && app.search !==""){
                        this.recordByMachine(app.stroj);
                    }

                    if(app.stav !=="0" && app.search !==""){
                        this.recordByState(app.stav);
                    }


                },
                goToPage: function(page) {
                    this.currentPage = page;
                },

                getStavText: function(stav_old) {
                    switch (stav_old) {
                        case "1":
                            return 'Nahlásená';
                        case "2":
                            return 'Pridelená';
                        case "3":
                            return 'Na kontrolu';
                        case "4":
                            return 'Vybavená';
                        default:
                            return '';
                    }
                },
            },

        })

    </script>

    <!-- jquery -->
    <script src="mdbootstrap/js/jquery-3.7.1.js"></script>

    </body>
    </html>

<?php function ikony_porucha(){ ?>
    <div class="container-fluid ikony d-flex col-md-12 col-12">
        <div class="row border0 justify-content-center ">
            <div class="col-md-3 col-4 cursor">
                <button class="btn ikona padding" title="Zobraziť" @click='viewRow(porucha.PoruchaID)'><i class="fa-regular fa-eye"></i></button>
            </div>

            <div class="col-md-3 col-4 cursor">
                <button class="btn ikona padding" title="Upraviť" @click='editRow(porucha.PoruchaID)'><i class="fa-regular fa-pen-to-square"></i></button>
            </div>

            <div class="col-md-3 col-4 cursor" id="zmazat_riadok">
                <button class="btn ikona padding" title="Vymazať" @click='deleteRow(porucha.PoruchaID)'><i class="fa-regular fa-trash-can"></i></button>
            </div>

            <div class="col-md-3 col-4 cursor" id="oprava_poruchy">
                <button class="btn ikona padding opravy" title="Oprava" @click='repair(porucha.PoruchaID)'><i class="fas fa-wrench icon"></i></button>
            </div>

        </div>

    </div>
    <?php
}
?>