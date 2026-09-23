<?php

namespace Database\Seeders;

use App\Core\Models\Module;
use App\Core\Models\Resource;
use App\Modules\Security\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $modules = [
            [
                'slug' => 'partner',
                'name' => 'Parceiro',
                'description' => 'Módulo de parceiros do sistema',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'resources' => [
                    [
                        'slug' => 'partnerTypes',
                        'name' => 'Tipos de parceiros',
                        'description' => 'Funcionalidades para tipos de parceiros',
                        'created_at' => $now,
                        'updated_at' => $now,
                        'permissions' => [
                            [
                                'action' => 'viewAny',
                                'label' => 'Listar',
                                'description' => 'Permite realizar a consulta de todos os tipos de parceiro',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'view',
                                'label' => 'Visualizar',
                                'description' => 'Permite visualizar os detalhes de um tipo de parceiro',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'create',
                                'label' => 'Criar',
                                'description' => 'Permite realizar a criação de um tipo de parceiro',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'update',
                                'label' => 'Editar',
                                'description' => 'Permite realizar a edição de um tipo de parceiro',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'delete',
                                'label' => 'Excluir',
                                'description' => 'Permite realizar a exclusão de um tipo de parceiro',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'export',
                                'label' => 'Exportar dados',
                                'description' => 'Permite realizar a exportação de dados dos tipos de parceiro',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'security',
                'name' => 'Segurança',
                'description' => 'Módulo de segurança do sistema (Autenticação, permissões etc.)',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'resources' => [
                    [
                        'slug' => 'users',
                        'name' => 'Usuários',
                        'description' => 'Funcionalidades para usuários',
                        'created_at' => $now,
                        'updated_at' => $now,
                        'permissions' => [
                            [
                                'action' => 'viewAny',
                                'label' => 'Listar',
                                'description' => 'Permite realizar a consulta de todos os usuários',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'view',
                                'label' => 'Visualizar',
                                'description' => 'Permite visualizar os detalhes de um usuário',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'create',
                                'label' => 'Criar',
                                'description' => 'Permite realizar a criação de um usuário',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'update',
                                'label' => 'Editar',
                                'description' => 'Permite realizar a edição de um usuário',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'delete',
                                'label' => 'Excluir',
                                'description' => 'Permite realizar a exclusão de um tipo de parceiro',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'export',
                                'label' => 'Exportar dados',
                                'description' => 'Permite realizar a exportação de dados dos usuários',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                        ]
                    ],
                    [
                        'slug' => 'roles',
                        'name' => 'Papéis',
                        'description' => 'Funcionalidades para papéis de acesso',
                        'created_at' => $now,
                        'updated_at' => $now,
                        'permissions' => [
                            [
                                'action' => 'viewAny',
                                'label' => 'Listar',
                                'description' => 'Permite realizar a consulta de todos os papéis',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'view',
                                'label' => 'Visualizar',
                                'description' => 'Permite visualizar os detalhes de um papel',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'create',
                                'label' => 'Criar',
                                'description' => 'Permite realizar a criação de um papel',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'update',
                                'label' => 'Editar',
                                'description' => 'Permite realizar a edição de um papel',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'delete',
                                'label' => 'Excluir',
                                'description' => 'Permite realizar a exclusão de um papel',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'definePermissions',
                                'label' => 'Definir permissões',
                                'description' => 'Permite definir as permissões relacionadas ao papel',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                            [
                                'action' => 'export',
                                'label' => 'Exportar dados',
                                'description' => 'Permite realizar a exportação de dados dos papéis',
                                'created_at' => $now,
                                'updated_at' => $now,
                            ],
                        ]
                    ]
                ]
            ]
        ];

        DB::transaction(function () use ($modules) {
            foreach ($modules as $moduleData) {
                $module = Module::firstOrCreate(
                    ['slug' => $moduleData['slug']],
                    ['name' => $moduleData['name'], 'description' => $moduleData['description'], 'is_active' => $moduleData['is_active'], 'created_at' => $moduleData['created_at'], 'updated_at' => $moduleData['updated_at']]
                );

                foreach ($moduleData['resources'] as $resourceData) {
                    $resource = Resource::firstOrCreate(
                        [
                            'slug' => $resourceData['slug']
                        ],
                        ['module_id' => $module->id, 'name' => $resourceData['name'], 'description' => $resourceData['description'], 'created_at' => $resourceData['created_at'], 'updated_at' => $resourceData['updated_at']]
                    );

                    foreach ($resourceData['permissions'] as $permissionData) {
                        $permissionName = $resource->slug.'.'.$permissionData['action'];
                        Permission::firstOrCreate(
                            [
                                'name' => $permissionName
                            ],
                            ['resource_id' => $resource->id, 'action' => $permissionData['action'], 'label' => $permissionData['label'], 'description' => $permissionData['description'], 'created_at' => $permissionData['created_at'], 'updated_at' => $permissionData['updated_at']]
                        );
                    }
                }
            }
        });
    }
}
