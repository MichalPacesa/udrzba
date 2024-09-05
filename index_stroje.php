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

    if(!strpos($_SERVER['HTTP_REFERER'], 'src/form/stroj.php')){
        $_SESSION["hlaska"] = "";
    }    

    if(ZistiPrava("Stroje",$dblink) == 0){
        echo "<span class='oznam'>Nemáte práva na zobrazenie strojov.</p>";
        exit;
    }

    ?>


    <?php /*if($_SESSION["hlaska"])	echo $_SESSION["hlaska"];// upozornenie */?>
    <div id='myapp'>
        <div class="container-fluid col-md-12 col-12">
            <div class="graybox row border0 d-flex align-items-end justify-content-center">
                 <div class="col-md-3 col-12">
                    <h2>Stroje</h2>
                </div>

                <div class="col-md-3 col-12 margintop20  filtre">
                    <input type='button' class="btn padding" @click='newRow' value='Nový stroj'>
                </div>

                <div class="marginbottom col-md-3 col-12 flex-column d-flex align-items-center justify-content-center roboto-light filtre">
                    Umiestnenie:
                    <br>
                    <select id=pozicia v-model="pozicia" class="form-select" class="select_height " @change="recordByPosition" >
                        <option value="" selected>Všetky</option>
                        <template v-for="position in positions">
                            <option v-bind:value="position.Stroj_umiestnenie" >{{position.Stroj_umiestnenie}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-3 col-12 d-flex align-items-end justify-content-center">
                    <div class="input-group flex-nowrap filtre hladaj">
                        <input type="text" class="form-control" placeholder="Hladať" id="search" v-model="search" v-on:keyup.esc="clearSearch" v-on:keyup.enter="recordBySearch">
                        <a @click='clearSearch'><i class="fa fa-times cursor zmaz"></i></a>
                        <button class="input-group-text" id="addon-wrapping" @click="recordBySearch"><i class="fa fa-search lupa cursor"></i></button>
                    </div>
                </div>

            </div>
        </div>
        

        <div class="mytable">
            <div class="container-fluid col-12 roboto-light">
                <div class="row tr border0" >
                    <div class="col-md-2 col-6 th">Evidenčné číslo</div>
                    <div class="col-md-2 col-6 th">Názov</div>
                    <div class="col-md-3 col-6 th">Popis</div>
                    <div class="col-md-2 col-6 th">Umiestnenie</div>
                    <div class="col-md-3 col-12 th"></div>
                </div>
                <div class="row tr" v-for="stroj in paginatedStroje">
                    <div class="col-md-2 col-6 td ">
                        {{ stroj.Stroj_evidencne_cislo }}&nbsp;
                    </div>
                    <div class="col-md-2 col-6 td ">
                        {{ stroj.Stroj_nazov }}&nbsp;
                    </div>
                    <div class="col-md-3 col-6 td " >
                        {{ stroj.Stroj_popis }}
                    </div>
                    <div class="col-md-2 col-6 td border0">
                        {{ stroj.Stroj_umiestnenie }}
                    </div>
                    <div class="col-md-3 col-12 td border0">
                        <?php
                        ikony_stroj();
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
        <transition name="notification2">
            <div v-if="notificationDelVisible && oznam_delete && 'Stroj_nazov' in oznam_delete" class="notification show">
                <span class="closebtn" @click="notificationDelVisible = false">&times;</span>
                <div class="text">
                    <i class="info-icon fas fa-info-circle"></i>
                    Stroj {{oznam_delete.Stroj_nazov}} bol vymazaný z databázy
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
            <?php include_once "src/partials/footer.php"; ?>
        </footer>
    </div>



    <!-- Script -->
    <script>

        var app = new Vue({
            el: '#myapp',
            data: {
                stroj: "",
                StrojID: 0,
                Stroj_nazov: "",
                Stroj_popis: "",
                Stroj_umiestnenie: "",
                search: "",
                selectlist: "",
                positions: "",
                pozicia: "",
                oznam_delete: {},
                hlaska: '',
                notificationVisible: false,
                notificationDelVisible: false,
                currentPage: 1,
                itemsPerPage: 6,
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
                paginatedStroje: function() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.stroj.slice(start, end);
                },
                totalPages: function() {
                    return Math.ceil(this.stroj.length / this.itemsPerPage);
                },
               /* paginatedStroje: function() {
                    return this.getPageData();
                }*/
            },

            methods: {
                allRecords: function(){
                    axios.get('read_stroje.php')
                        .then(function (response) {
                            app.stroj = response.data;
                           
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
                    if(app.search !=="" && this.stroj === ""){
                        axios.get('read_stroje.php', {
                            params: {
                                search: app.search
                            }
                        })
                            .then(function (response) {
                                app.stroj = response.data;
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                    if(app.search !=="" && this.stroj !== ""){
                        axios.get('read_stroje.php', {
                            params: {
                                search: app.search,
                                pozicia: this.pozicia
                            }
                        })
                            .then(function (response) {
                                app.stroj = response.data;
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
                            axios.get('read_stroje.php', {
                                params: {
                                    pozicia: this.pozicia,
                                    search: app.search
                                }
                            })
                                .then(function (response) {
                                    app.stroj = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }
                        else{
                            axios.get('read_stroje.php', {
                                params: {
                                    pozicia: this.pozicia,
                                }
                            })
                                .then(function (response) {
                                    app.stroj = response.data;
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }

                    }
                },
                listPosition: function(){
                    axios.get('read_stroje.php', {
                        params: {
                            list: "Stroj_umiestnenie"
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
                    var stroj = this.stroj.find(emp => emp.StrojID === id);
                    if (stroj) {
                        var c = confirm("Ste si istý, že chcete zmazať stroj e.č."+stroj.Stroj_nazov+"?" );
                        if (c) {
                            axios.get('zmazat_stroj.php', {
                                params: {
                                    StrojID: stroj.StrojID,
                                    Stroj_nazov: stroj.Stroj_nazov

                                }
                            })
                                .then(function (response) {
                                    app.oznam_delete = response.data;
                                    console.log(response.data);
                                    app.allRecords();
                                    app.notificationDelVisible = true;
                                    //alert(app.notificationDelVisible);
                                    setTimeout(() => app.notificationDelVisible = false, 5000);

                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                        }
                    }
                    return ;
                },

                editRow: function(StrojID){
                    location.replace("src/form/stroj.php?StrojID="+StrojID);

                },

                viewRow: function(StrojID){
                    location.replace("src/form/stroj.php?StrojID="+StrojID+"&zobrazit=1");
                },

                newRow: function(){
                    location.replace("src/form/stroj.php");
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
                    return this.stroj.slice(start, end);
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

<?php function ikony_stroj(){ ?>

    <div class="container-fluid ikony d-flex col-md-12 col-12">
        <div class="row border0 justify-content-center ">
            <div class="col-md-4 col-4 cursor">
                <button class="btn ikona padding" title="Zobraziť" @click='viewRow(stroj.StrojID)'><i class="fa-regular fa-eye"></i></button>
            </div>

            <div class="col-md-4 col-4 cursor">
                <button class="btn ikona padding" title="Upraviť" @click='editRow(stroj.StrojID)'><i class="fa-regular fa-pen-to-square"></i></button>
            </div>

            <div class="col-md-4 col-4 cursor" id="zmazat_riadok">
                <button class="btn ikona padding" title="Vymazať" @click='deleteRow(stroj.StrojID)'><i class="fa-regular fa-trash-can"></i></button>
            </div>
        </div>

    </div>
    <?php
}
?>