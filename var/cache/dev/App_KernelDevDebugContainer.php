<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerGXxqaFw\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerGXxqaFw/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerGXxqaFw.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerGXxqaFw\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerGXxqaFw\App_KernelDevDebugContainer([
    'container.build_hash' => 'GXxqaFw',
    'container.build_id' => 'f3d8563d',
    'container.build_time' => 1717756448,
    'container.runtime_mode' => \in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) ? 'web=0' : 'web=1',
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerGXxqaFw');
