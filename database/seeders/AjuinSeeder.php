<?php

namespace Database\Seeders;

use App\Models\MaintenanceType;
use App\Models\Store;
use App\Models\Ticket;
use App\Models\Tier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AjuinSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // ── Hapus user lama yang sudah tidak punya role (karena rolenya baru dihapus oleh RoleSeeder) ──
        $idCol = 'id';
        User::doesntHave('roles')
            ->where($idCol, '!=', 1) // Lindungi user ID 1 jika dibutuhkan, meski biasanya aman
            ->each(fn (User $u) => $u->forceDelete());

        // ── Master data toko + akun Keptok (kepala toko) ──────────
        // Format: 'Nama Toko' => 'email login keptok'. Satu toko = satu akun Keptok.
        $stores = [
            // Jawa Timur
            'Tunjungan Plaza'          => 'heavenscenttpsby@gmail.com',
            'Pakuwon Trade Center'     => 'heavenscentptcsurabaya@gmail.com',
            'Royal'                    => 'heavenscentroyal@gmail.com',
            'Mall Olympic Garden'      => 'heavenscentmallolympicgarden@gmail.com',
            'Malang Town Square'       => 'heavenscentmatos01@gmail.com',
            'Unimas District'          => 'heavenscentunimasdistrict@gmail.com',
            'Gresik Mall'              => 'heavenscentgressmall@gmail.com',
            'Suncity Sidoarjo'         => 'suncityheavenscent@gmail.com',
            'Sunrise Mojokerto'        => 'heavenscentsunrisemjk@gmail.com',
            'Kediri Town Square'       => 'heavenscentkediri@gmail.com',
            'Kediri Mall'              => 'heavenscentkedirimall@gmail.com',
            'Lippo Jember'             => 'jemberhs@gmail.com',
            'Food Junction'            => 'foodjunctionheavenscent@gmail.com',
            'Lippo Batu'               => 'lippobatuheavenscent@gmail.com',
            'Malang City Point'        => 'heavenscentmcp@gmail.com',
            'BG Junction'              => 'heavenscentbgjunction@gmail.com',
            // Jawa Tengah / DIY
            'Artos Magelang'           => 'heavenscent.artos@gmail.com',
            'Rita Purwokerto'          => 'heavenscentrita@gmail.com',
            'Suncity Madiun'           => 'heavenscentsuncitymadiun@gmail.com',
            'Solo Square'              => 'hsolosquare@gmail.com',
            'Pakuwon Mall Solo'        => 'heavenscentpakuwonsolo@gmail.com',
            'The Park Solo'            => 'heavenscenttheparksolo@gmail.com',
            'Sleman City Hall'         => 'slemancityheavenscent@gmail.com',
            'Plaza Malioboro'          => 'hsmalioboro@gmail.com',
            'Jogja City Mall'          => 'hsjogjacitymall@gmail.com',
            'Plaza Ambarrukmo'         => 'pameranhsaeonbsd@gmail.com',
            'Paragon Semarang'         => 'heavenscentparagonsmg@gmail.com',
            'The Park Semarang'        => 'heavenscenttheparksmg@gmail.com',
            'Ciputra Semarang'         => 'heavenscentciputrasmg@gmail.com',
            '23 Semarang'              => 'heavenscent23semarang@gmail.com',
            'Pacific Mall Tegal'       => 'tegalkasir2@gmail.com',
            'Lippo Pekalongan'         => 'heavenscentlippopekalongan@gmail.com',
            // Jawa Barat / Bandung Raya
            'Jatinangor Town Square'   => 'jatosheavenscent@gmail.com',
            'Ciwalk Bandung'           => 'heavenscentciwalk@gmail.com',
            'The Kings Bandung'        => 'thekingbandung@gmail.com',
            'CSB Mall'                 => 'heavenscentsuperblock@gmail.com',
            'Grage Mall'               => 'heavenscentgrage@gmail.com',
            'TSM Bandung'              => 'tsmheavenscent@gmail.com',
            'Citylink Bandung'         => 'heavenscentcitylink@gmail.com',
            'Asia Plaza Sumedang'      => 'heavenscentasiaplaza@gmail.com',
            'Bandung Indah Plaza'      => 'heavenscentbip@gmail.com',
            'Summarecon Bandung'       => 'heavenscentsummaba@gmail.com',
            "D'Botanica Bandung"       => 'heavenscentbotanica@gmail.com',
            'Citimall Garut'           => 'heavenscentcitimallgarut@gmail.com',
            'Ciplaz Garut'             => 'heavenscentciplazgarut@gmail.com',
            // Jabodetabek
            'Green Pramuka Square'     => 'heavenscentgreenpramukasquare@gmail.com',
            'Grand Galaxy Park'        => 'heavenscentgrandgalaxypark@gmail.com',
            'Mall Bassura'             => 'heavenscentbasuramall@gmail.com',
            'Lippo Ekalokasari Bogor'  => 'heavenscentlippoekalokasari@gmail.com',
            'Ciputra Cibubur'          => 'heavenscentciputracibubur@gmail.com',
            'Gandaria City'            => 'gandariacityheavenscent@gmail.com',
            'LW Kota Wisata'           => 'heavenscentlwkw@gmail.com',
            'LW Grand Wisata'          => 'heavenscentlwgw@gmail.com',
            'FX Sudirman'              => 'heavenscentfxsudirman@gmail.com',
            'Margo City'               => 'margocityheavenscent@gmail.com',
            'Karawang Central Park'    => 'heavenscentkcpkarawang@gmail.com',
            'Resinda Mall'             => 'heavenscentresindamall@gmail.com',
            'TSM Cibubur'              => 'heavenscentsmcibubur@gmail.com',
            'Tangerang City Mall'      => 'tangerangcityheavenscent@gmail.com',
            'Cibinong City Mall'       => 'heavenscentcibinongmall@gmail.com',
            'Grand Metropolitan'       => 'heavenscentgrandmetropolitan@gmail.com',
            'Botani Square'            => 'botanisquareheavenscent@gmail.com',
            'LW Alam Sutera'           => 'hslivingworldalamsutera@gmail.com',
            'Mal Artha Gading'         => 'heavenscentmag@gmail.com',
            'Metropolitan Mall Bekasi' => 'hsmetropolitanmall@gmail.com',
            'Puri Indah Mall'          => 'heavenscentpuriindahmall@gmail.com',
            'The Park Sawangan'        => 'heavenscenttheparkswg@gmail.com',
            'Lippo Kemang'             => 'lippokemangheavenscent@gmail.com',
            'Lippo Kramat Jati'        => 'heavenscentlippokramatjati@gmail.com',
            'Mall Of Serang'           => 'heavenscentmallofserang@gmail.com',
            'Lippo Mall Puri'          => 'heavenscentlippomallpuri@gmail.com',
            'Lippo Cikarang'           => 'heavenscentlippocikarang@gmail.com',
            'Green Sedayu Mall'        => 'heavenscentgreensedayu@gmail.com',
            // Bali
            'TSM Bali'                 => 'heavenscenttsmbali@gmail.com',
            'Discovery Mall Bali'      => 'heavenscentdiscoverymall@gmail.com',
            'Level 21 Bali'            => 'heavenscentlevel21bali@gmail.com',
            'LW Denpasar'              => 'heavenscentlwdenpasar@gmail.com',
            // Sumatera / Medan
            'Manhattan Mall Medan'     => 'heavenscentmanhattanmedan@gmail.com',
            'Delipark Mall Medan'      => 'heavenscentdeliparkmedan@gmail.com',
            'Centre Park Medan'        => 'keptok.centreparkmedan@ajuin.test',   // placeholder, email toko belum ada
            'Plaza Medan Fair'         => 'keptok.plazamedanfair@ajuin.test',    // placeholder, email toko belum ada
        ];

        foreach ($stores as $storeName => $keptokEmail) {
            $code = 'HS-' . Str::upper(Str::slug($storeName));

            /** @var Store $store */
            $store = Store::firstOrCreate(
                ['code' => $code],
                ['name' => $storeName, 'public_token' => (string) Str::uuid()],
            );

            // Sinkronkan nama bila master berubah
            if ($store->name !== $storeName) {
                $store->update(['name' => $storeName]);
            }

            // Akun Keptok (kepala toko): satu per toko, login pakai email toko
            /** @var User $keptok */
            $keptok = User::firstOrCreate(
                ['email' => $keptokEmail],
                ['name' => 'Keptok ' . $storeName, 'password' => Hash::make('password'), 'is_active' => true],
            );
            $keptok->syncRoles(['Keptok']);
            $keptok->scopes()->delete();
            $keptok->scopes()->create(['scope_type' => 'STORE', 'store_id' => $store->id]);
            $keptok->stores()->sync([$store->id]);
        }

        // ── Struktur wilayah (sumber: sheet "formasi abk" — Kuota HRGA Provinsi) ──
        // Region → SPV pemegang + daftar toko (nama toko merujuk master $stores di atas).
        // GA/HRGA di-assign per Region (mengikuti granularity kolom Region di Excel).
        $regions = [
            'Jatim 1'  => ['spv' => 'Meme',    'stores' => ['Tunjungan Plaza', 'Suncity Sidoarjo', 'Mall Olympic Garden', 'Lippo Batu', 'Kediri Town Square', 'Kediri Mall']],
            'Jatim 3'  => ['spv' => 'Meme',    'stores' => ['BG Junction', 'Unimas District']],
            'Jatim 2'  => ['spv' => 'Via',     'stores' => ['Malang Town Square', 'Malang City Point', 'Royal', 'Pakuwon Trade Center', 'Gresik Mall', 'Food Junction', 'Sunrise Mojokerto', 'Lippo Jember']],
            'Jateng 1' => ['spv' => 'Dewi',    'stores' => ['Suncity Madiun', 'Solo Square', 'Pakuwon Mall Solo', 'The Park Solo', 'Jogja City Mall', 'Plaza Malioboro', 'Plaza Ambarrukmo', 'Sleman City Hall']],
            'Jateng 2' => ['spv' => 'Nadea',   'stores' => ['Artos Magelang', 'Paragon Semarang', 'The Park Semarang', 'Ciputra Semarang', 'Rita Purwokerto', '23 Semarang', 'Lippo Pekalongan', 'Pacific Mall Tegal']],
            'Jabar 1'  => ['spv' => 'Manda',   'stores' => ['CSB Mall', 'Grage Mall', 'Jatinangor Town Square', 'Asia Plaza Sumedang', 'TSM Bandung', 'Ciwalk Bandung', 'Summarecon Bandung']],
            'Jabar 2'  => ['spv' => 'Marham',  'stores' => ['Citylink Bandung', 'Bandung Indah Plaza', "D'Botanica Bandung", 'The Kings Bandung', 'Citimall Garut', 'Ciplaz Garut', 'Karawang Central Park', 'Resinda Mall']],
            'Jabo 1'   => ['spv' => 'Lilis',   'stores' => ['Cibinong City Mall', 'LW Kota Wisata', 'LW Grand Wisata', 'Grand Galaxy Park', 'Grand Metropolitan', 'Metropolitan Mall Bekasi', 'Lippo Kramat Jati', 'Botani Square']],
            'Jabo 2'   => ['spv' => 'Silvy',   'stores' => ['Gandaria City', 'FX Sudirman', 'Tangerang City Mall', 'Green Pramuka Square', 'Margo City', 'Mall Of Serang', 'Lippo Cikarang', 'Mall Bassura', 'LW Alam Sutera']],
            'Jabo 3'   => ['spv' => 'Ayu',     'stores' => ['Lippo Ekalokasari Bogor', 'TSM Cibubur', 'Ciputra Cibubur', 'Lippo Kemang', 'Mal Artha Gading', 'Puri Indah Mall', 'Lippo Mall Puri', 'Green Sedayu Mall', 'The Park Sawangan']],
            'Bali'     => ['spv' => 'Angeline','stores' => ['TSM Bali', 'Discovery Mall Bali', 'Level 21 Bali', 'LW Denpasar']],
            'Medan'    => ['spv' => 'Yoga',    'stores' => ['Manhattan Mall Medan', 'Delipark Mall Medan', 'Centre Park Medan', 'Plaza Medan Fair']],
        ];

        // Email SPV (dari daftar email yang diberikan). Yoga belum ada email → placeholder.
        $spvEmails = [
            'Meme'     => 'supervisormeme30@gmail.com',
            'Via'      => 'supervisorvia559@gmail.com',
            'Dewi'     => 'supervisordewi@gmail.com',
            'Nadea'    => 'supervisornadea@gmail.com',
            'Manda'    => 'supervisormanda@gmail.com',
            'Marham'   => 'supervisormarham@gmail.com',
            'Lilis'    => 'lilissupervisor@gmail.com',
            'Silvy'    => 'spvhsjabodetabek2@gmail.com',
            'Ayu'      => 'spvhsjabodetabek3@gmail.com',
            'Angeline' => 'spvheavenscentbali@gmail.com',
            'Yoga'     => 'spv.yoga@ajuin.test',
        ];

        // ── GA / HRGA (sumber: sheet "September" kolom GA) ────────
        // 3 GA, tiap GA memegang beberapa Region:
        //   Rizky  → Jatim + Bali | Stenly → Jateng + Jabar | Muvar → Jabodetabek + Medan
        $gaData = [
            'Rizky'  => ['email' => 'taufiq.rizky753@gmail.com', 'regions' => ['Jatim 1', 'Jatim 2', 'Jatim 3', 'Bali']],
            'Stenly' => ['email' => 'stenlysidupa12@gmail.com',  'regions' => ['Jateng 1', 'Jateng 2', 'Jabar 1', 'Jabar 2']],
            'Muvar'  => ['email' => 'muvarvarha@gmail.com',      'regions' => ['Jabo 1', 'Jabo 2', 'Jabo 3', 'Medan']],
        ];

        foreach ($gaData as $gaName => $gaInfo) {
            // Kumpulkan semua toko dari region yang dipegang GA ini
            $gaStoreNames = [];
            foreach ($gaInfo['regions'] as $regionName) {
                $gaStoreNames = array_merge($gaStoreNames, $regions[$regionName]['stores']);
            }

            /** @var User $ga */
            $ga = User::firstOrCreate(
                ['email' => $gaInfo['email']],
                ['name' => 'GA ' . $gaName, 'password' => Hash::make('password'), 'is_active' => true],
            );
            $ga->syncRoles(['HRGA']);

            $ga->scopes()->delete();
            $storeIds = Store::whereIn('name', $gaStoreNames)->pluck('id');
            foreach ($storeIds as $storeId) {
                $ga->scopes()->create(['scope_type' => 'STORE', 'store_id' => $storeId]);
            }
            $ga->stores()->sync($storeIds->all());
        }

        // ── SPV: gabungkan toko dari semua region yang dipegang SPV yang sama ──
        $spvStores = [];
        foreach ($regions as $info) {
            $spvStores[$info['spv']] = array_merge($spvStores[$info['spv']] ?? [], $info['stores']);
        }

        foreach ($spvStores as $spvName => $storeNames) {
            /** @var User $spvUser */
            $spvUser = User::firstOrCreate(
                ['email' => $spvEmails[$spvName] ?? 'spv.' . Str::slug($spvName) . '@ajuin.test'],
                ['name' => 'SPV ' . $spvName, 'password' => Hash::make('password'), 'is_active' => true],
            );
            $spvUser->syncRoles(['SPV']);

            $spvUser->scopes()->delete();
            $storeIds = Store::whereIn('name', $storeNames)->pluck('id');
            foreach ($storeIds as $storeId) {
                $spvUser->scopes()->create(['scope_type' => 'STORE', 'store_id' => $storeId]);
            }
            $spvUser->stores()->sync($storeIds->all());
        }

        // ── Super Admin ──────────────────────────────────────────
        /** @var User $admin */
        $admin = User::firstOrCreate(
            ['email' => 'admin@ajuin.test'],
            ['name' => 'System Super Admin', 'password' => Hash::make('password'), 'is_active' => true],
        );
        $admin->syncRoles(['Super Admin']);
        $admin->scopes()->updateOrCreate(
            ['scope_type' => 'ALL'],
            ['store_id' => null]
        );

        // ── Tier & deadline maintenance ────────────────────────────
        $tierA = Tier::firstOrCreate(['name' => 'A'], ['deadline_days' => 3]);
        $tierB = Tier::firstOrCreate(['name' => 'B'], ['deadline_days' => 7]);
        $tierC = Tier::firstOrCreate(['name' => 'C'], ['deadline_days' => 14]);

        // ── Jenis Maintenance (berelasi ke tier) ───────────────────
        // Format: 'Nama Tier' => ['Jenis maintenance', ...]
        $maintenanceTypesByTier = [
            'A' => [
                'Kaca display pecah',
                'Logo / signage pecah',
                'Logo / signage mati',
                'Lampu display mati',
            ],
            'B' => [
                'List akrilik pecah',
                'HPL bolong',
                'Finishing kaca retak',
                'Engsel rusak',
                'Pengunci kabinet rusak',
            ],
            'C' => [
                'Penambahan storage',
                'Penggantian wifi',
                'Repaint interior karena kusam',
                'Penambahan interior',
            ],
        ];

        $tiersByName = ['A' => $tierA, 'B' => $tierB, 'C' => $tierC];
        foreach ($maintenanceTypesByTier as $tierName => $typeNames) {
            foreach ($typeNames as $typeName) {
                MaintenanceType::firstOrCreate(
                    ['name' => $typeName],
                    ['tier_id' => $tiersByName[$tierName]->id],
                );
            }
        }

        // ── Sample ticket ────────────────────────────────────────
        if (Ticket::query()->doesntExist()) {
            $idCol = 'id';
            /** @var Store|null $firstStore */
            $firstStore = Store::query()->orderBy($idCol)->first();
            if ($firstStore) {
                $ticket = Ticket::create([
                    'store_id'      => $firstStore->id,
                    'ticket_number' => Ticket::nextNumber(),
                    'submitted_by'  => 'Demo User',
                    'jabatan'       => 'Kepala Toko',
                    'type'          => 'PEMBELIAN_PERALATAN',
                    'source'        => 'USER_SUBMISSION',
                    'description'   => 'Contoh pengajuan restock diffuser untuk validasi dashboard.',
                    'status'        => 'SCREENING',
                ]);
                $ticket->logs()->create(['to_status' => 'SCREENING', 'note' => 'Ticket dibuat dari seeder demo.']);
            }
        }
    }
}
