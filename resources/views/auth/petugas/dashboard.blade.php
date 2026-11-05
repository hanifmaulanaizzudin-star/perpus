<h1>Dashboard Petugas</h1>
<p>Selamat datang di sistem perpustakaan.</p>

<form action="{{ route('petugas.logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>