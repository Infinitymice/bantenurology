<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Spin Wheel</title>
    <!---------------  Chart JS  --------------------->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <!---------------  Chart JS Plugin  --------------------->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.1.0/chartjs-plugin-datalabels.min.js"></script>
    <style>
        /*-----------------  GOOGLE FONTS  -----------------*/
        @import url("https://fonts.googleapis.com/css2?family=PT+Serif&display=swap");

        /*-----------------  VARIABLES  -----------------*/
        :root {
            --white_color: rgb(255, 255, 255);
            --gold_color: rgb(255, 215, 0);
            --green_color: rgb(45, 252, 26);
            --body_background: linear-gradient(to right, #141e30, #243b55);
            --spin_background: linear-gradient(to right, #fc4a1a, #f7b733);
        }

        /*-----------------  Base  -----------------*/
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
            font-family: "PT Serif", serif;
        }

        body {
            height: 100vh;
        }

        /*-----------------  Styling Start  -----------------*/
        h1 {
            position: absolute;
            font-size: 4rem;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--gold_color);
        }

        #text {
            color: gold !important; /* Warna emas */
            font-size: 3rem; /* Ukuran font besar */
            font-weight: bold; /* Teks tebal */
            text-align: center; /* Teks berada di tengah */
            text-shadow: 2px 2px 5px rgb(255, 255, 255); /* Efek bayangan */
            z-index: 1000; /* Pastikan di atas elemen lain */
            position: relative; /* Pastikan posisinya tetap */
            margin-top: 20px; /* Tambahkan margin */
        }

        .container {
            width: 90%;
            max-width: 700px; /* Perbesar ukuran container */
            margin-top: 0;
            max-height: 90vh;
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            padding: 3rem;
            padding-top: 0;
            border-radius: 1rem;
        }

        .wheel_box {
            position: relative;
            width: 100%;
            height: 100%;
            max-width: 700px; /* Sesuaikan ukuran wheel */
            max-height: 700px; /* Sesuaikan ukuran wheel */
            margin: 0 auto;
        }

        .confetti {
            position: absolute;
            width: 10px; /* Lebar confetti */
            height: 10px; /* Tinggi confetti */
            opacity: 0.8;
            pointer-events: none; /* Hindari interaksi mouse */
            animation: fall linear forwards; /* Animasi jatuh */
            top: -10px; /* Mulai di luar viewport atas */
        }

        #spinWheel {
            max-height: inherit;
            width: inherit;
            transform: rotate(270deg);
        }

        #spin_btn {
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            height: 30%;
            width: 30%;
            border-radius: 50%;
            cursor: pointer;
            border: 0;
            background: var(--spin_background);
            color: var(--white_color);
            text-transform: uppercase;
            font-size: 1.2rem;
            letter-spacing: 0.1rem;
            font-weight: 600;
        }

        @keyframes fall {
            0% {
                transform: translateY(-100%);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh);
                opacity: 0;
            }
        }

        .confetti {
            position: absolute;
            width: 10px; /* Width of the confetti */
            height: 10px; /* Height of the confetti */
            opacity: 0.8;
            pointer-events: none; /* Prevent mouse events */
            animation: fall linear forwards; /* Animation for falling */
        }

        .triangle-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 50px 50px 0 0; /* Ukuran segitiga */
            border-color: gray transparent transparent transparent; /* Warna segitiga abu-abu */
            cursor: pointer; /* Ganti kursor menjadi pointer */
            z-index: 1000; /* Pastikan berada di atas elemen lain */
        }

        .triangle-back:hover {
            border-color: darkgray transparent transparent transparent; /* Warna segitiga saat di-hover */
        }

        .background-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Video menyesuaikan layar */
            z-index: -1; /* Di belakang konten lainnya */
        }

        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Warna overlay dengan transparansi */
            mix-blend-mode: multiply; /* Opsi efek pencampuran */
            z-index: 0; /* Tetap di depan video, tapi di belakang konten lainnya */
        }
    </style>
</head>

<body>
<video class="background-video" autoplay muted loop>
    <source src="{{ asset('video/bg_vid.mp4') }}" type="video/mp4" />
    Your browser does not support the video tag.
</video>
<div class="video-overlay"></div>
<a href="{{ route('admin.setting-wheel') }}" class="triangle-back"></a>
    <div>
        <p id="text"></p>
    </div>
    <div class="container">
        <div style="display: flex; text-align: center">
            <img style="
                margin: auto;
                font-size: 50px;
                color: greenyellow;
                position: relative;
                top: 30px;
                height: 50px;
                z-index: 100;" class="fa fa-sort-desc"
                src="https://media.geeksforgeeks.org/wp-content/uploads/20240108170123/Untitled-1.png" alt="">
        </div>
        <div class="wheel_box">
            <canvas id="spinWheel" width="600" height="600"></canvas>
            <button id="spin_btn">Spin</button>
        </div>
    </div>

    <!---------------  SCRIPT  --------------------->
    <script>
        // Get the names from the server-side
        let defaultNames = @json($registrasis->pluck('full_name'));

        /* --------------- Spin Wheel  --------------------- */
        const spinWheel = document.getElementById("spinWheel");
        const spinBtn = document.getElementById("spin_btn");
        const text = document.getElementById("text");
        let spinValues = [];
        const size = Array(defaultNames.length).fill(10); // Equal size for each participant

        const spinColors = [
            "#E74C3C", "#7D3C98", "#2E86C1", "#138D75", "#F1C40F", 
            "#D35400", "#138D75", "#F1C40F", "#b163da", "#E74C3C"
        ];

        let spinChart = new Chart(spinWheel, {
            plugins: [ChartDataLabels],
            type: "pie",
            data: {
                labels: defaultNames,
                datasets: [{
                    backgroundColor: spinColors,
                    data: size,
                }],
            },
            options: {
                responsive: true,
                animation: { duration: 0 },
                plugins: {
                    tooltip: false,
                    legend: { display: false },
                    datalabels: {
                        color: "#ffffff",
                        formatter: (_, context) => {
                            return context.chart.data.labels[context.dataIndex]; // Show label text
                        },
                        font: (context) => {
                            const labels = context.chart.data.labels;
                            const longestLabel = labels.reduce((a, b) => (a.length > b.length ? a : b), "");
                            const baseFontSize = 20;
                            const maxLength = longestLabel.length;
                            let fontSize = Math.max(baseFontSize - (maxLength * 0.6), 15);
                            return { size: fontSize };
                        },
                        anchor: "center",
                        align: "center",
                        rotation: 90,
                    },
                },
            },
        });

        /* --------------- Spinning Code --------------------- */
        let count = 0;
        let resultValue = 101;
        spinBtn.addEventListener("click", () => {
    spinBtn.disabled = true;
    text.innerHTML = `<p>Best Of Luck!</p>`;
    
    // Tentukan sudut acak untuk putaran roda
    let randomDegree = Math.floor(Math.random() * (355 - 0 + 1) + 0);

    let rotationInterval = window.setInterval(() => {
        // Update rotasi roda
        spinChart.options.rotation = spinChart.options.rotation + resultValue;
        spinChart.update();
        
        // Cek apakah rotasi sudah mencapai atau melebihi 360 derajat
        if (spinChart.options.rotation >= 360) {
            count += 1;
            resultValue -= 5;
            spinChart.options.rotation = 0;
        } else if (count > 15 && spinChart.options.rotation >= randomDegree) {
            // Setelah putaran selesai, tampilkan nama yang sesuai
            generateValue(spinChart.options.rotation);
            clearInterval(rotationInterval);
            count = 0;
            resultValue = 101;
        }
    }, 10);
});

        /* --------------- Display Value Based On The Angle --------------------- */
        const generateValue = (angleValue) => {
        const sectionCount = defaultNames.length; // Jumlah bagian
        const angleStep = 360 / sectionCount; // Ukuran sudut setiap bagian

        // Sudut awal Chart.js (270 derajat searah jarum jam dari posisi atas)
        const startingAngle = 270;

        // Normalisasi sudut ke rentang 0-360
        const normalizedAngle = (startingAngle + angleValue) % 360;

        // Hitung index berdasarkan sudut
        const index = Math.floor((360 - normalizedAngle) / angleStep) % sectionCount;

        // Menampilkan nama yang terpilih
        text.innerText = "Congratulations to: " + defaultNames[index] + "!";

        // Memanggil animasi confetti
        createConfetti();
        spinBtn.disabled = false;
    };



        function createConfetti() {
            const colors = ["#FF0D72", "#0DC2FF", "#0DFF72", "#F0FF0D", "#FF7F0D"];
            const confettiCount = 100; // Number of confetti pieces

            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement("div");
                confetti.className = "confetti";
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * window.innerWidth + "px"; // Random horizontal position
                confetti.style.animationDuration = Math.random() * 2 + 2 + "s"; // Random duration
                confetti.style.animationDelay = Math.random() * 2 + "s"; // Random delay
                document.body.appendChild(confetti);

                // Remove the confetti after the animation ends
                confetti.addEventListener("animationend", () => {
                    confetti.remove();
                });
            }
        }
    </script>
</body>

</html>