type Props = { on: boolean; onChange: (on: boolean) => void; label?: string };

export default function Switch({ on, onChange, label = "منشور" }: Props) {
  return <button className="sw" type="button" role="switch" aria-checked={on} aria-label={label} onClick={() => onChange(!on)} />;
}
