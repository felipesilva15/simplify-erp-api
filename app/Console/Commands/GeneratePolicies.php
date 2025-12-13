<?php

namespace App\Console\Commands;

use App\Modules\Auth\Models\Permission;
use Illuminate\Console\Command;

class GeneratePolicies extends Command
{
    protected $signature = 'acl:generate-policies';
    protected $description = 'Generate policies based on permissions table';

    public function handle()
    {
        $permissions = Permission::all()
            ->groupBy('resource');

        foreach ($permissions as $resource => $perms) {
            $policyName = Str::studly(Str::singular($resource)) . 'Policy';
            $modelName  = Str::studly(Str::singular($resource));

            $methods = $perms->map(function ($perm) {
                return $this->policyMethod($perm->action, $perm->name);
            })->implode("\n\n");

            $stub = <<<PHP
<?php

namespace App\Policies;

use App\Models\\{$modelName};
use App\Models\User;

class {$policyName}
{
{$methods}
}
PHP;

            file_put_contents(
                app_path("Policies/{$policyName}.php"),
                $stub
            );

            $this->info("Policy {$policyName} generated");
        }
    }

    protected function policyMethod(string $action, string $ability): string
    {
        $withModel = !in_array($action, ['viewAny', 'create']);

        return $withModel
            ? <<<PHP
    public function {$action}(User \$user, \$model)
    {
        return \$user->can('{$ability}');
    }
PHP
            : <<<PHP
    public function {$action}(User \$user)
    {
        return \$user->can('{$ability}');
    }
PHP;
    }
}
