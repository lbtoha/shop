@setup
    $APP_ID = 'quiz';
    $REPO_URL = 'git@github.com:Softivus-Laravel/quiz-backend.git';
    $REPO_BRANCH = 'demo';
    $user = 'deployer';
    $WORK_DIR = '/var/www/quiz/admin';

    function logMessage($message) {
    return "echo '\033[32m" .$message. "\033[0m';\n";
    }
@endsetup

@servers(['prod' => ['deployer@86.48.1.24']])

@story('deploy')
    start-agent
    update-code
    install-dependencies
    build-assets
    set-permissions
    migrate-db
    restart-workers
@endstory

@task('start-agent')
    eval "$(ssh-agent -s)"
    ssh-add ~/.ssh/id_ed25519
@endtask

@task('update-code')
    cd {{ $WORK_DIR }}
    git stash
    git pull origin {{ $REPO_BRANCH }}
@endtask


@task('install-dependencies')
    cd {{ $WORK_DIR }}
    composer install --no-interaction --optimize-autoloader
@endtask

@task('build-assets')
    cd {{ $WORK_DIR }}
    npm install
    npm run build
@endtask

@task('set-permissions')
    {{ logMessage('Set permissions') }}
    chmod -R ug+rwx {{ $WORK_DIR }}/storage {{ $WORK_DIR }}/bootstrap/cache
@endtask

@task('migrate-db')
    {{ logMessage('Migrating database') }}
    cd {{ $WORK_DIR }}
    php artisan migrate --force
    php artisan optimize
@endtask

@task('restart-workers')
    sudo supervisorctl restart all
    sudo systemctl restart nginx
@endtask

@finished
    echo "✅ Envoy deployment finished.\r\n";
@endfinished
