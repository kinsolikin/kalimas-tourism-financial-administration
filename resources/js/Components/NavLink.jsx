import { Link } from '@inertiajs/react';

export default function NavLink({ active = false, className = '', children, ...props }) {
    return (
        <Link
            {...props}
            className={`relative inline-flex items-center px-4 py-2 text-sm font-semibold transition duration-200 ease-in-out
                ${
                    active
                        ? 'text-blue-600 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-blue-600'
                        : 'text-gray-500 hover:text-blue-600 hover:after:w-full hover:after:bg-blue-500 after:transition-all after:duration-300 after:ease-in-out after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-blue-500'
                }
                ${className}`}
        >
            {children}
        </Link>
    );
}
