document.getElementById('formDaftar').addEventListener('submit', async function (e) {
    e.preventDefault();

    const nis = document.getElementById('nis').value.trim();
    const nama = document.getElementById('nama').value.trim();
    const divisi = document.getElementById('divisi').value;
    const email = document.getElementById('email').value.trim();

    const errNis = document.getElementById('errNis');
    const msgBox = document.getElementById('responseMessage');
    const btnSubmit = document.getElementById('btnSubmit');

    // Validasi Client-Side menggunakan RegEx
    // NIS harus tepat 8 digit angka
    const nisRegex = /^[0-9]{8}$/;

    if (!nisRegex.test(nis)) {
        errNis.classList.remove('hidden');
        return;
    } else {
        errNis.classList.add('hidden');
    }

    // JSON Payload
    const payload = {
        nis: nis,
        nama: nama,
        divisi: divisi,
        email: email
    };

    try {
        // Mengubah tombol menjadi status loading
        btnSubmit.disabled = true;
        btnSubmit.innerText = 'MENGIRIM...';

        const response = await fetch('api.php?action=daftar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        msgBox.classList.remove('hidden');

        if (result.status === 'success') {

            msgBox.className =
                'mt-4 p-3 rounded-lg text-sm text-center font-medium ' +
                'bg-emerald-950 border border-emerald-500 text-emerald-300';

            msgBox.innerText = result.message;

            // Reset form setelah berhasil
            document.getElementById('formDaftar').reset();

        } else {

            msgBox.className =
                'mt-4 p-3 rounded-lg text-sm text-center font-medium ' +
                'bg-rose-950 border border-rose-500 text-rose-300';

            msgBox.innerText = result.message;
        }

    } catch (error) {

        console.error('Fetch Error:', error);

        msgBox.classList.remove('hidden');

        msgBox.className =
            'mt-4 p-3 rounded-lg text-sm text-center font-medium ' +
            'bg-rose-950 border border-rose-500 text-rose-300';

        msgBox.innerText =
            'Terjadi kesalahan koneksi ke server backend!';

    } finally {

        // Mengembalikan tombol seperti semula
        btnSubmit.disabled = false;
        btnSubmit.innerText = 'KIRIM PENDAFTARAN';
    }
});
