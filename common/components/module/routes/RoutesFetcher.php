<?php

namespace common\components\module\routes;

use common\components\module\Modules;
use RuntimeException;

final class RoutesFetcher
{

    private $modules;

    public function __construct(Modules $modules)
    {
        $this->modules = $modules;
    }

    public function getRules(): array
    {
        $groups = $this->getGroups();

        usort($groups, static function(Group $a, Group $b) {
            return $b->priority <=> $a->priority;
        });

        return array_merge(
            ...array_map(
                static function (Group $group) { return $group->rules; },
                $groups
            )
        );
    }

    /**
     * @return Group[]
     */
    private function getGroups(): array
    {
        /** @var Group[] $groups */
        $groups = [];

        foreach ($this->modules->definitions() as $name => $definition) {
            $class = $definition['class'] ?? '';
            if ($class === '') {
                throw new RuntimeException('Undefined class for module ' . $name);
            }
            if (!is_subclass_of($class, RoutesProvider::class)) {
                continue;
            }
            $groups[] = new Group($class::routes(), $class::routesPriority());
        }

        return $groups;
    }
}
