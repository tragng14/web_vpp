<div class="my-4">
    <h2 class="mb-3"><?= htmlspecialchars($data["pageData"]["title"]) ?></h2>

    <div class="content text-format">
        <?= htmlspecialchars($data["pageData"]["content"]) ?>
    </div>
</div>


<style>
.text-format {
    white-space: pre-line;         /* Giữ xuống dòng từ CSDL */
    font-family: "Segoe UI", Arial, sans-serif;  /* Font đẹp, hiện đại */
    font-size: 17px;               /* Cỡ chữ vừa mắt */
    line-height: 1.8;              /* Giãn dòng đẹp */
    text-align: justify;           /* Căn đều hai bên */
    text-justify: inter-word;      /* Tránh bị so le chữ */
    color: #333;                   /* Màu chữ hài hoà */
    margin-bottom: 30px;
}

.text-format p {
    margin-bottom: 15px;           /* Khoảng cách giữa các đoạn */
}

.text-format img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 20px auto;             /* Căn giữa ảnh */
}

</style>
