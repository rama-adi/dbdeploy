import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type SSOSubmit = {
    databaseId: number;
};

type Database = {
    id: number;
    name: string;
    databaseName: string;
};

export default function Dashboard({ databases }: { databases: Database[] }) {
    const { post, processing } = useForm<Required<SSOSubmit>>({
        databaseId: 0,
    });

    const loginSSO = (databaseId: number) => {
        post(
            route('sso.login.id', {
                databaseInfo: databaseId,
            }),
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="px-6 py-4">
                <h1 className="mb-4 text-2xl font-bold">Dashboard</h1>
                <p className="mb-4">Welcome to the dashboard! Here you can find an overview of your databases.</p>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-1/4">Database Name</TableHead>
                            <TableHead className="w-1/4">Database</TableHead>
                            <TableHead className="w-1/4">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {databases.map((database) => (
                            <TableRow key={database.id}>
                                <TableCell>{database.name}</TableCell>
                                <TableCell>
                                    <span className="font-mono">{database.databaseName}</span>
                                </TableCell>
                                <TableCell>
                                    <Button
                                        onClick={() => loginSSO(database.id)}
                                        type="submit"
                                        className="mt-4 w-full"
                                        tabIndex={4}
                                        disabled={processing}
                                    >
                                        {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                        PhpMyAdmin
                                    </Button>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </AppLayout>
    );
}
