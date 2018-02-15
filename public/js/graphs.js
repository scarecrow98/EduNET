window.onload = () => {

    showDiagrams();
    function showDiagrams() {
        let canvas = document.getElementById('canvas1');
        let ctx = canvas.getContext('2d');
        let data = document.getElementsByClassName('data-holder');
        ctx.fillStyle = '#edf2f6';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        for (let i = 0; i < data.length; i++) {
            let val = data[i].value;
            let angle = (360 / 100) * val;
            let offset = canvas.width / data.length;
            let radius = offset * 0.41;
            let pos = { x: i * offset + offset / 2, y: 150 };
            // let color = {
            //     r: Math.floor(Math.random() * 256),
            //     g: Math.floor(Math.random() * 256),
            //     b: Math.floor(Math.random() * 256),
            // };


            ctx.beginPath();
            ctx.arc(pos.x, pos.y, radius, 0, 2 * Math.PI);
            ctx.fillStyle = '#c6c6c6';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(pos.x, pos.y, radius, toDeg(0), toDeg(angle));
            ctx.lineWidth = 16;
            // ctx.strokeStyle = 'rgb('+ color.r +','+ color.g +','+ color.b +')';
            ctx.strokeStyle = '#2a9edd';
            ctx.stroke();

            ctx.font = '20px Georgia';
            ctx.fillStyle = '#000';
            ctx.textAlign = 'center';
            ctx.fillText(val + '%', pos.x, pos.y);
        }
    }

    showCart();
    function showCart() {
        let canvas = document.getElementById('canvas2');
        let ctx = canvas.getContext('2d');

        let data = [
            {
                name: '10.A',
                value: 34
            },
            {
                name: '11.A',
                value: 45
            },
            {
                name: '12.A',
                value: 18
            },
            {
                name: '13.A',
                value: 19
            },
            {
                name: '9.C',
                value: 38
            },
            {
                name: '5.A',
                value: 56
            }
        ];
        let sum = 0;
        for (d of data) { sum += d.value; }
        let lastAngle = 0;

        let radius = 200;

        let colors = [
            '#779cab',
            '#03b5aa',
            '#4286f4',
            '#f4e542',
            '#e52222',
            '#42c2f4',
            '#d17ff9'
        ];

        ctx.fillStyle = '#edf2f6';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        for (let i = 0; i < data.length; i++) {
            // let color = {
            //     r: Math.floor(Math.random() * 256),
            //     g: Math.floor(Math.random() * 256),
            //     b: Math.floor(Math.random() * 256),
            // };


            let percent = (data[i].value / sum) * 100;
            let angle = (360 / 100) * percent;
            let textPos = {
                x: Math.cos(toDeg(lastAngle + angle / 2)) * (radius + 40) + canvas.width / 2,
                y: Math.sin(toDeg(lastAngle + angle / 2)) * (radius + 40) + canvas.height / 2
            };

            ctx.beginPath();
            ctx.moveTo(canvas.width / 2, canvas.height / 2);
            ctx.arc(canvas.width / 2, canvas.height / 2, radius, toDeg(lastAngle), toDeg(lastAngle + angle));
            ctx.fillStyle = colors[i];
            //ctx.fillStyle = 'rgb('+ color.r +','+ color.g +','+ color.b +')';
            ctx.fill();

            ctx.font = '20px Georgia';
            ctx.fillStyle = '#000';
            ctx.textAlign = 'center';
            ctx.fillText(percent.toFixed(2) + '% (' + data[i].value + ')', textPos.x, textPos.y);

            let offset = canvas.width / data.length;
            let labelPos = {
                x: i * offset + offset / 2,
                y: canvas.height - 30
            };

            ctx.fillStyle = colors[i];
            ctx.fillRect(labelPos.x-15, labelPos.y-10, 10, 10);

            ctx.font = '14px Georgia';
            ctx.fillStyle = '#000';
            ctx.textAlign = 'left';
            ctx.fillText(data[i].name, labelPos.x, labelPos.y);

            lastAngle += angle;
        }

        ctx.beginPath();
        ctx.arc(canvas.width / 2, canvas.height / 2, 80, 0, 2 * Math.PI);
        ctx.fillStyle = '#edf2f6';
        ctx.fill();

    }

    //radiánból fokba váltó függvény
    function toDeg(deg) {
        //90-et kivonok az átadott fokból, hogy a függöleges tengely tetejétől számolja a 0 fokot
        return (deg - 90) * (Math.PI / 180);
    }
}


class Diagram