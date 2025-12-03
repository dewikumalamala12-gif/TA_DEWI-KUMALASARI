<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sudah Absen</title>
<style>
/* full screen overlay */
.alert-center{
    display:flex;
    align-items:center;
    justify-content:center;
    height:100vh;
    background:rgba(0,0,0,0.35);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* box utama */
.alert-box{
    background: linear-gradient(135deg, #0a1a2f, #1f3b5f);
    padding:30px 24px;
    border-radius:16px;
    text-align:center;
    box-shadow:0 12px 28px rgba(0,0,0,0.5);
    max-width:420px;
    width:90%;
    color:#fff;
    border:2px solid #ff9900;
}

/* icon */
.alert-box .icon{
    font-size:50px;
    margin-bottom:12px;
}

/* judul */
.alert-box h3{
    margin:10px 0;
    color:#ffcc00;
    font-size:22px;
    text-shadow: 1px 1px 2px #000;
}

/* teks info */
.alert-box p{
    font-size:15px;
    color:#e0e0e0;
    line-height:1.4;
}

/* tombol kembali */
.alert-btn{
    display:inline-block;
    margin-top:18px;
    padding:12px 20px;
    border-radius:8px;
    background:#ff6600;
    color:#fff;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}

.alert-btn:hover{
    background:#ff8533;
    box-shadow:0 4px 12px rgba(0,0,0,0.4);
}
</style>
</head>
<body>

<div class="alert-center">
    <div class="alert-box">
        <div class="icon">⚠️</div>
        <h3>Absensi Sudah Tercatat</h3>
        <p>Anda sudah absen hari ini. Anda dapat melakukan absen kembali besok. TERIMAKASIH</p>
       <a href="index.php" class="alert-btn" id="btnMengerti">Mengerti</a>
    </div>
</div>

</body>
</html>
