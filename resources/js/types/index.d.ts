export interface User {
    id: number;
    name: string;
    email: string;
    is_admin?: boolean;
    email_verified_at?: string;
}

export interface SiteSettings {
    [key: string]: string;
}

export interface SkillGroup {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    sort_order: number;
    is_visible: boolean;
    skills_count?: number;
    visible_skills?: Skill[];
    skills?: Skill[];
}

export interface Skill {
    id: number;
    skill_group_id?: number | null;
    name: string;
    slug: string;
    note?: string | null;
    proficiency?: string | null;
    sort_order: number;
    is_visible: boolean;
    group?: { id: number; name: string } | null;
}

export interface ProjectImage {
    id: number;
    project_id: number;
    path: string;
    alt_text?: string | null;
    caption?: string | null;
    sort_order: number;
}

export interface Project {
    id: number;
    title: string;
    slug: string;
    summary?: string | null;
    body?: string | null;
    category?: string | null;
    role?: string | null;
    problem?: string | null;
    solution?: string | null;
    features?: string | null;
    challenges?: string | null;
    outcome_or_learning?: string | null;
    demo_url?: string | null;
    source_url?: string | null;
    documentation_url?: string | null;
    cover_image?: string | null;
    meta_title?: string | null;
    meta_description?: string | null;
    is_featured: boolean;
    status: 'draft' | 'published' | 'archived';
    sort_order: number;
    published_at?: string | null;
    created_at?: string;
    updated_at?: string;
    skills?: Skill[];
    images?: ProjectImage[];
}

export interface Experience {
    id: number;
    organization: string;
    role: string;
    type?: string | null;
    location_mode?: string | null;
    start_date: string;
    end_date?: string | null;
    is_current: boolean;
    summary?: string | null;
    responsibilities?: string[] | null;
    outcomes?: string[] | null;
    sort_order: number;
    is_visible: boolean;
}

export interface Education {
    id: number;
    institution: string;
    qualification: string;
    field_of_study?: string | null;
    start_date: string;
    end_date?: string | null;
    expected_end_date?: string | null;
    status: 'in_progress' | 'completed' | 'dropped';
    description?: string | null;
    sort_order: number;
    is_visible: boolean;
}

export interface Certificate {
    id: number;
    name: string;
    issuer: string;
    issued_at?: string | null;
    credential_url?: string | null;
    credential_id?: string | null;
    image?: string | null;
    is_visible: boolean;
    sort_order: number;
}

export interface NavigationLink {
    id: number;
    placement: 'header' | 'footer';
    label: string;
    url: string;
    is_external: boolean;
    sort_order: number;
    is_visible: boolean;
}

export interface SocialLink {
    id: number;
    label: string;
    platform: string;
    url: string;
    sort_order: number;
    is_visible: boolean;
}

export interface ContactMessage {
    id: number;
    name: string;
    email: string;
    subject?: string | null;
    message: string;
    status: 'unread' | 'read' | 'archived';
    created_at: string;
    read_at?: string | null;
}

export interface ActivityLog {
    id: number;
    user_id?: number | null;
    action: string;
    entity_type?: string | null;
    entity_id?: number | null;
    summary: string;
    created_at: string;
    user?: { id: number; name: string } | null;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from?: number | null;
    to?: number | null;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};
