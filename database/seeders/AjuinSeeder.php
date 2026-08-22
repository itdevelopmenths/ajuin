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

        // ── SPV dan toko-toko yang mereka bawahi ──────────────────
        // Format: 'NamaSPV' => ['Nama Toko', ...]
        $spvData = [
            'Meme'    => ['TP', 'Suncity SDA', 'Ketos', 'KM', 'Unimas', 'BGJ', 'MOG', 'Lippo Batu'],
            'Via'     => ['PTC', 'Royal', 'Gressmall', 'Matos', 'Sunrise', 'Food Junction', 'MCP', 'Lippo Jember'],
            'Dewi'    => ['Suncity Madiun', 'Solo Square', 'Pakuwon Solo', 'The Park Solo', 'JCM', 'Malioboro', 'SCH', 'Amplaz'],
            'Nadea'   => ['Artos', 'Paragon Semarang', 'The Park Semarang', 'Ciputra Semarang', 'Rita', 'Pacific Mall Tegal', '23 Semarang'],
            'Manda'   => ['CSB', 'Grage', 'Jatos', 'Asia Plaza', 'TSM Bandung', 'Ciwalk', 'Summarecon Bandung'],
            'Marham'  => ['Festival Citylink', 'BIP', 'Botanica', 'The Kings', 'KCP', 'Resinda'],
            'Lilis'   => ['GGP', 'GMM', 'MM', 'LWKW', 'LWGW', 'CCM', 'Botani'],
            'Silvy'   => ['GPS', 'Bassura', 'Gancit', 'Margo', 'FX', 'Tangcity', 'LW Alsut'],
            'Ayu'     => ['Ekalokasari', 'Ciputra Cibubur', 'TSM Cibubur', 'Kemang', 'The Park Sawangan', 'Puri Indah', 'MAG'],
            'Angelin' => ['TSM Bali', 'Discovery Mall Bali'],
        ];

        foreach ($spvData as $spvName => $storeNames) {
            /** @var User $spvUser */
            $spvUser = User::firstOrCreate(
                ['email' => 'spv.' . Str::slug($spvName) . '@ajuin.test'],
                ['name' => 'SPV ' . $spvName, 'password' => Hash::make('password'), 'is_active' => true],
            );
            $spvUser->syncRoles(['SPV']);

            // Reset scope lama
            $spvUser->scopes()->delete();
            $spvUser->stores()->sync([]);

            $assignedStoreIds = [];
            foreach ($storeNames as $storeName) {
                $code = 'HS-' . Str::upper(Str::slug($storeName));

                /** @var Store $store */
                $store = Store::firstOrCreate(
                    ['code' => $code],
                    ['name' => $storeName, 'public_token' => (string) Str::uuid()],
                );

                $assignedStoreIds[] = $store->id;

                $spvUser->scopes()->create([
                    'scope_type' => 'STORE',
                    'store_id'   => $store->id,
                ]);
            }

            $spvUser->stores()->sync($assignedStoreIds);
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
