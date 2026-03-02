<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="light">
    <div class="sidebar-brand">
        <a href="./index.html" class="brand-link">
            <img src="./public/img/logo_rs.png" alt="AdminLTE Logo" class="brand-image shadow" />
            <span class="brand-text fw-bold">FARMASI REBORN</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-item @if ($menu == 'Dashboard') menu-open @endif">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>
                            Dashboard
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('indexdashboard') }}"
                                class="nav-link @if ($menu == 'Dashboard') active @endif">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Dashboard Farmasi </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">DEPO FARMASI</li>
                <li class="nav-item">
                    <a href="{{ route('indexpelayananresep') }}"
                        class="nav-link @if ($menu == 'indexpelayananresep') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Pelayanan Resep</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexcarisep') }}"
                        class="nav-link @if ($menu == 'indexcarisep') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Riwayat Pelayanan</p>
                    </a>
                </li>
                <li class="nav-header">Apotek Online</li>
                <li class="nav-item">
                    <a href="{{ route('indexcarisepaptonline') }}"
                        class="nav-link @if ($menu == 'indexcarisep_apotek') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Cari SEP</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexdaftarresep') }}"
                        class="nav-link @if ($menu == 'indexdaftarresep') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Daftar Resep</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexriwayatpelayananonline') }}"
                        class="nav-link @if ($menu == 'indexriwayatpelayananonline') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Riwayat Pelayanan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexdataklaim') }}"
                        class="nav-link @if ($menu == 'indexdataklaim') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Data Klaim</p>
                    </a>
                </li>
                <li hidden class="nav-item">
                    <a href="{{ route('indexrekapprb') }}"
                        class="nav-link @if ($menu == 'indexrekapprb') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Rekap Peserta PRB</p>
                    </a>
                </li>
                <li  class="nav-header">Vclaim</li>
                <li  class="nav-item">
                    <a href="{{ route('indexcreatesep') }}"
                        class="nav-link @if ($menu == 'indexcreatesep') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Create SEP</p>
                    </a>
                </li>
                <li class="nav-header">Gudang Farmasi</li>
                <li class="nav-item">
                    <a href="{{ route('indexterimabarangpo') }}"
                        class="nav-link @if ($menu == 'indexterimabarangpo') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Purchase Order</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterstok') }}"
                        class="nav-link @if ($menu == 'indexmasterstok') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Log kartu stok</p>
                    </a>
                </li>
                <li class="nav-header">Data Master</li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterdpho') }}"
                        class="nav-link @if ($menu == 'indexmasterdpho') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master DPHO</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmappingbarang') }}"
                        class="nav-link @if ($menu == 'indexmappingbarang') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Mapping Master Barang</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterbarang') }}"
                        class="nav-link @if ($menu == 'indexmasterbarang') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master Barang</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterobatbpjs') }}"
                        class="nav-link @if ($menu == 'indexmasterobatbpjs') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master Obat Bpjs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmastersupplier') }}"
                        class="nav-link @if ($menu == 'indexmastersupplier') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master Supplier Obat</p>
                    </a>
                </li>
                <li class="nav-header">Data Laporan</li>
                <li class="nav-item">
                    <a href="{{ route('indexrencanapengadaanbarang') }}"
                        class="nav-link @if ($menu == 'indexrencanapengadaanbarang') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Pengadaan Barang</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="" class="nav-link @if ($menu == 'masterkasir') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Laporan Mutasi Obat (Masuk/Keluar)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link @if ($menu == 'masterkasir') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Laporan Stok Opname</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link @if ($menu == 'masterkasir') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Laporan Obat Kadaluarsa/Expired Date (ED)</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexlaporanmasterpengadaan') }}" class="nav-link @if ($menu == 'laporanpengadaanbarang') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Laporan Pembelian Barang</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link @if ($menu == 'masterkasir') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Laporan Analisis Persediaan</p>
                    </a>
                </li> --}}

                <li class="nav-header">INFO AKUN</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-person-vcard"></i>
                        <p class="text">Detail Akun</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="logout()">
                        <i class="nav-icon bi bi-box-arrow-left"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
