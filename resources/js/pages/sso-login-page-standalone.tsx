import AppLogoIcon from '@/components/app-logo-icon';
import InputError from '@/components/input-error';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Head, Link, useForm } from '@inertiajs/react';
import { AlertCircle, Database, LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

interface StandaloneLoginPageProps {
    loggedOut: boolean;
    databases: {
        id: number;
        name: string;
        databaseName: string;
        username: string;
    }[];
}

interface CustomLoginForm {
    username: string;
    password: string;
}

export default function Login({ loggedOut, databases }: StandaloneLoginPageProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<CustomLoginForm>>({
        username: '',
        password: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('sso.login'), {
            onFinish: () => reset('password'),
        });
    };
    return (
        <div className="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div className="flex w-full max-w-md flex-col gap-6">
                <Link href={route('home')} className="flex items-center gap-2 self-center font-medium">
                    <div className="flex h-9 w-9 items-center justify-center">
                        <AppLogoIcon className="size-9 fill-current text-black dark:text-white" />
                    </div>
                </Link>

                <div className="flex flex-col gap-6">
                    <Card className="rounded-xl">
                        <CardHeader className="px-10 pt-8 pb-0 text-center">
                            <CardTitle className="text-xl">Database Login</CardTitle>
                            <CardDescription>Choose your database or use manual login for other databases.</CardDescription>
                        </CardHeader>
                        <CardContent className="px-10 py-2">
                            <Head title="Log in to your database" />
                            {loggedOut && (
                                <Alert variant="default">
                                    <AlertCircle className="h-4 w-4" />
                                    <AlertTitle>Logged out from PHPMyAdmin</AlertTitle>
                                    <AlertDescription>
                                        You have been logged out from PHPMyAdmin. Please log in again to access your database.
                                    </AlertDescription>
                                </Alert>
                            )}
                            <Tabs defaultValue="my">
                                <TabsList className="my-4 grid w-full grid-cols-2">
                                    <TabsTrigger value="my">My Database</TabsTrigger>
                                    <TabsTrigger value="manual">Manual login</TabsTrigger>
                                </TabsList>
                                <div className="min-h-[200px]">
                                    <TabsContent value="my">
                                        <p className="text-muted-foreground mb-4 text-sm">Click on your database to log in.</p>
                                        {databases.map((database) => (
                                            <Link
                                                href={route('sso.login.id', { databaseInfo: database.id })}
                                                method="post"
                                                className="focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border-input bg-background hover:bg-accent hover:text-accent-foreground inline-flex w-full items-start gap-4 rounded-md border px-4 py-3 text-left text-sm font-medium shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:opacity-50 has-[>svg]:px-3 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"
                                            >
                                                <div className="bg-secondary inline-flex items-center justify-center rounded-md p-2">
                                                    <Database className="h-6 w-6" />
                                                </div>
                                                <div className="w-full">
                                                    <h2 className="mb-2 text-xl font-semibold">{database.name}</h2>
                                                    <p>
                                                        Database name is&nbsp;<span className="font-mono font-semibold">{database.databaseName}</span>
                                                        &nbsp;and the associated user is{' '}
                                                        <span className="font-mono font-semibold">{database.username}</span>
                                                    </p>
                                                </div>
                                            </Link>
                                        ))}
                                    </TabsContent>
                                    <TabsContent value="manual">
                                        <p className="text-muted-foreground mb-4 text-sm">
                                            Use this option if your database is not listed. You just need the username and password of the database
                                            you want to access.
                                        </p>

                                        <div className="w-full">
                                            <form className="flex flex-col gap-6" onSubmit={submit}>
                                                <div className="grid gap-6">
                                                    <div className="grid gap-2">
                                                        <Label htmlFor="email">Database address</Label>
                                                        <Input
                                                            id="username"
                                                            type="text"
                                                            required
                                                            autoFocus
                                                            tabIndex={1}
                                                            autoComplete="username"
                                                            value={data.username}
                                                            onChange={(e) => setData('username', e.target.value)}
                                                        />
                                                        <InputError message={errors.username} />
                                                    </div>

                                                    <div className="grid gap-2">
                                                        <div className="flex items-center">
                                                            <Label htmlFor="password">Database Password</Label>
                                                        </div>
                                                        <Input
                                                            id="password"
                                                            type="password"
                                                            required
                                                            tabIndex={2}
                                                            autoComplete="current-password"
                                                            value={data.password}
                                                            onChange={(e) => setData('password', e.target.value)}
                                                            placeholder="Password"
                                                        />
                                                        <InputError message={errors.password} />
                                                    </div>

                                                    <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                                                        {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                                        Log in
                                                    </Button>
                                                </div>
                                            </form>
                                        </div>
                                    </TabsContent>
                                </div>
                            </Tabs>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    );
}
