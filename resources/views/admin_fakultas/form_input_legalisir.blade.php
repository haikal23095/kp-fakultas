<form action="{{ route('admin_fakultas.legalisir.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Pilih Mahasiswa/Alumni</label>
        <select name="id_user_mahasiswa" class="form-control select2" required>
            @foreach($daftarMahasiswa as $mhs)
                <option value="{{ $mhs->Id_User }}">{{ $mhs->NIM }} - {{ $mhs->Nama_Mahasiswa }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Jenis Dokumen</label>
        <select name="jenis_dokumen" class="form-control" required>
            <option value="Ijazah">Ijazah</option>
            <option value="Transkrip">Transkrip</option>
        </select>
    </div>

    <div class="form-group">
        <label>Jumlah Salinan</label>
        <input type="number" name="jumlah_salinan" class="form-control" min="1" max="20" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan & Mulai Proses</button>
</form>