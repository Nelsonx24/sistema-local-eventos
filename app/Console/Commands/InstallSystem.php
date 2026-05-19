<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Staff;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallSystem extends Command
{
    protected $signature = 'app:install-system';

    protected $description = 'Instalar el sistema Gran Cañaveral';

    public function handle()
    {
        $this->info('=====================================');
        $this->info('  INSTALANDO SISTEMA GRAN CAÑAVERAL');
        $this->info('=====================================');

        try {
            DB::connection()->getPdo();
            $this->info('✓ Conexión a MySQL establecida');
        } catch (\Exception $e) {
            $this->error('✗ Error: No se puede conectar a MySQL');
            $this->error('  Verifica tu archivo .env y que MySQL esté ejecutándose');

            return 1;
        }

        $this->info('Creando tablas...');

        Schema::create('staff', function ($t) {
            $t->id();
            $t->string('name');
            $t->string('role');
            $t->string('username')->unique();
            $t->string('password');
            $t->string('email');
            $t->string('status')->default('Active');
            $t->string('avatar');
            $t->rememberToken();
            $t->timestamps();
        });
        $this->info('  ✓ staff');

        Schema::create('events', function ($t) {
            $t->id();
            $t->string('client_name');
            $t->string('client_id');
            $t->string('event_type');
            $t->date('date');
            $t->string('status')->default('Pendiente');
            $t->string('payment_status', 20)->default('pending');
            $t->string('event_status', 20)->default('upcoming');
            $t->decimal('total_amount', 12, 2);
            $t->decimal('advance_payment', 12, 2);
            $t->decimal('balance_pending', 12, 2);
            $t->date('payment_due_date');
            $t->string('signed_contract_url')->nullable();
            $t->string('registered_by')->nullable();
            $t->softDeletes();
        });
        $this->info('  ✓ events');

        Schema::create('inventory', function ($t) {
            $t->id();
            $t->string('name');
            $t->string('category');
            $t->integer('boxes')->default(0);
            $t->integer('units_per_box')->default(1);
            $t->integer('loose_units')->default(0);
            $t->decimal('price_per_box', 10, 2);
            $t->decimal('price_per_unit', 10, 2);
            $t->string('status')->default('In Stock');
            $t->timestamps();
        });
        $this->info('  ✓ inventory');

        Schema::create('sales', function ($t) {
            $t->id();
            $t->string('event_id')->nullable();
            $t->string('client_name');
            $t->decimal('amount', 12, 2);
            $t->decimal('cash_received', 12, 2)->default(0);
            $t->decimal('change_given', 12, 2)->default(0);
            $t->date('date');
            $t->string('payment_method');
            $t->string('status')->default('Paid');
            $t->string('seller_name')->nullable();
            $t->boolean('is_printed')->default(false);
            $t->timestamps();
        });
        $this->info('  ✓ sales');

        Schema::create('sale_items', function ($t) {
            $t->id();
            $t->foreignId('sale_id')->constrained()->onDelete('cascade');
            $t->string('name');
            $t->integer('quantity');
            $t->string('type');
            $t->decimal('subtotal', 12, 2);
            $t->timestamps();
        });
        $this->info('  ✓ sale_items');

        Schema::create('assets', function ($t) {
            $t->id();
            $t->string('name');
            $t->string('category');
            $t->integer('quantity')->default(1);
            $t->string('condition')->default('Bueno');
            $t->date('last_maintenance')->nullable();
            $t->timestamps();
        });
        $this->info('  ✓ assets');

        Schema::create('config', function ($t) {
            $t->id();
            $t->string('key')->unique();
            $t->text('value')->nullable();
            $t->timestamps();
        });
        $this->info('  ✓ config');

        $this->info('Insertando usuarios...');

        Staff::create([
            'name' => 'Administrador',
            'email' => 'admin@grancanaveral.com',
            'username' => 'admin',
            'password' => 'admin123',
            'role' => 'Administrador',
            'status' => 'Active',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Admin',
        ]);

        Staff::create([
            'name' => 'Vendedor Demo',
            'email' => 'vendedor@grancanaveral.com',
            'username' => 'vendedor',
            'password' => 'vendedor123',
            'role' => 'Vendedor',
            'status' => 'Active',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Vendedor',
        ]);

        Staff::create([
            'name' => 'CM Demo',
            'email' => 'cm@grancanaveral.com',
            'username' => 'cm',
            'password' => 'cm123',
            'role' => 'CM',
            'status' => 'Active',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=CM',
        ]);
        $this->info('  ✓ Usuarios creados');

        Config::set('event_types', json_encode(['Boda', 'Corporativo', 'Cumpleaños', 'Social']));
        Config::set('qr', 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=Demo-Payment');
        $this->info('  ✓ Configuración inicial');

        $this->info('');
        $this->info('=====================================');
        $this->info('  INSTALACIÓN COMPLETA');
        $this->info('=====================================');
        $this->info('');
        $this->info('Usuario: admin');
        $this->info('Password: admin123');
        $this->info('');
        $this->info('Ahora ejecuta: php artisan serve');
        $this->info('Y visita: http://localhost:8000');

        return 0;
    }
}
