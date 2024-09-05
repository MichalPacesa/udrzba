<title>Údržba IS </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<!-- vue a axios -->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
		
<!-- bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

<!-- mdbootstrap -->
<link rel="stylesheet" href="../../mdbootstrap/css/mdb.min.css" />
<link rel="stylesheet" href="../../mdbootstrap/css/all.css">

 <!-- Google Fonts Roboto -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

<!-- MDB script-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.2.0/mdb.umd.min.js" ></script>

<!-- CSS štýl -->

<style>
    <?php include __DIR__ . '/../../css/style.css'; ?>
    <?php include __DIR__ . '/../../css/style_rd.css'; ?>
    <?php include __DIR__ . '/../../css/style_roboto.css'; ?>
</style>
	
<script>
	/* po kliknuti na ikonku mobilneho menu - zobrazi alebo zmaze mobilne menu*/
	function menuPreMobily(){

		var d = document.getElementById("navbarExample01");
		//alert(d);
		if(d.className=="navbar-collapse collapse")
			d.className += " show";
		else if(d.className=="navbar-collapse collapse show")
			d.classList.remove('show');
		
	}
</script>

 <script>
function ShowPassword() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>